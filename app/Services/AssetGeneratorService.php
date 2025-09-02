<?php

namespace App\Services;

use App\Mail\VerificationMail;
use App\Models\Decision;
use App\Models\Defender;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Policy;
use App\Models\Rule;
use App\Models\Tag;
use App\Models\Target;
use App\Models\User;
use App\Models\Word;
use App\Models\Wordlist;
use App\Validators\API\DecisionValidator;
use App\Validators\API\DefenderValidator;
use App\Validators\API\GroupValidator;
use App\Validators\API\PermissionValidator;
use App\Validators\API\PolicyValidator;
use App\Validators\API\RuleValidator;
use App\Validators\API\TagValidator;
use App\Validators\API\TargetValidator;
use App\Validators\API\UserValidator;
use App\Validators\API\WordlistValidator;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;

class AssetGeneratorService
{
    private static $totalAsset = 0;

    private static $totalResource = 0;
    
    private static $failResource = 0;

    public static function perform(string $path)
    {
        $assets = Yaml::parse($path);
        $assetNames = [
            'decisions' => [self::class, 'generateDecision'],
            'defenders' => [self::class, 'generateDefender'],
            'groups' => [self::class, 'generateGroup'],
            'permissions' => [self::class, 'generatePermission'],
            'policies' => [self::class, 'generatePolicy'],
            'rules' => [self::class, 'generateRule'],
            'tags' => [self::class, 'generateTag'],
            'targets' => [self::class, 'generateTarget'],
            'users' => [self::class, 'generateUser'],
            'wordlists' => [self::class, 'generateWordlist'],
        ];
        $result = [];
        foreach ($assetNames as $assetName => $assetCallback)
        {
            self::process($assets, $assetName, $assetCallback, $result);
        }
        return [
            'result' => $result,
            'total_asset' => self::$totalAsset,
            'total_resource' => self::$totalResource,
            'fail_resource' => self::$failResource,
        ];
    }

    private static function process(array $assets, string $name, callable $callback, array &$result)
    {
        if (isset($assets[$name]) && $assets[$name] != null)
        {
            self::$totalAsset++;
            if (isset($result[$name]))
            {
                $result[$name] = [];
            }
            foreach ($assets[$name] as $asset)
            {
                $result[$name][] = $callback($asset);
            }
        }
    }

    private static function bypass($resource, $action)
    {
        return IdentificationService::can(
            IdentificationService::get(),
            $resource,
            $action,
        );
    }

    private static function sync(array $data, array &$relationships, string $name, callable $callback)
    {
        $result = [
            'status' => true,
            'ids' => [],
        ];
        if (isset($data[$name]) && $data[$name] != null)
        {
            if (!isset($relationships[$name]))
            {
                $relationships[$name] = [];
            }
            foreach ($data[$name] as $asset)
            {
                $relationships[$name][] = $callback($asset, false);
            }
            foreach ($relationships[$name] as $relationship)
            {
                if (!$relationship['status'])
                {
                    $result['status'] = false;
                }
                if ($relationship['id'] != null)
                {
                    $result['ids'][] = $relationship['id'];
                }
            }
        }
        return $result;
    }

    private static function generateDecision(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('decision', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, DecisionValidator::build($data));
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $validated['action_configuration'] = match ($validated['action'])
        {
            'kill' => implode(',', [$validated['kill_header'], $validated['kill_path']]),
            'redirect' => $validated['redirect'],
            default => null,
        };
        $relationships = [];
        if ($recursive)
        {
            if (isset($data['wordlist']) && $data['wordlist'] != null)
            {
                if (isset($relationships['wordlist']))
                {
                    $relationships['wordlist'] = [];
                }
                $result = self::generateWordlist($data['wordlist'], false);
                $relationships['wordlist'][] = $result;
                if (!$result['status'])
                {
                    self::$failResource++;
                    return [
                        'status' => false,
                        'id' => null,
                        'errors' => null,
                        'relationships' => $relationships,
                    ];
                }
                if ($result['id'] != null)
                {
                    $validated['wordlist_id'] = $result['id'];
                }
            }
            $defenderResult = self::sync($data, $relationships, 'defenders', [self::class, 'generateDefender']);
            $tagResult = self::sync($data, $relationships, 'tags', [self::class, 'generateTag']);
            if (!$defenderResult['status'] || !$tagResult['status'])
            {
                self::$failResource++;
                return [
                    'status' => false,
                    'id' => null,
                    'errors' => null,
                    'relationships' => $relationships,
                ];
            }
        }
        $decision = Decision::create($validated);
        if (isset($validated['defender_ids']))
        {
            $decision->defenders()->sync($validated['defender_ids']);
        }
        TagFieldService::syncTags($validated, $decision);
        if ($recursive)
        {
            $decision->defenders()->sync($defenderResult['ids']);
            $decision->tags()->sync($tagResult['ids']);
        }
        return [
            'status' => true,
            'id' => $decision->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    private static function generateDefender(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('defender', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, DefenderValidator::build());
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        if (isset($validated['certification']))
        {
            $validated['certification'] = DefenderAPIService::decodeBase64($validated['certification']);
        }
        $relationships = [];
        if ($recursive)
        {
            $decisionResult = self::sync($data, $relationships, 'decisions', [self::class, 'generateDecision']);
            $groupResult = self::sync($data, $relationships, 'groups', [self::class, 'generateGroup']);
            $tagResult = self::sync($data, $relationships, 'tags', [self::class, 'generateTag']);
            if (!$decisionResult['status'] || !$groupResult['status'] || !$tagResult['status'])
            {
                self::$failResource++;
                return [
                    'status' => false,
                    'id' => null,
                    'errors' => null,
                    'relationships' => $relationships,
                ];
            }
        }
        $defender = Defender::create($validated);
        if (isset($validated['group_ids']))
        {
            $defender->groups()->sync($validated['group_ids']);
        }
        if (isset($validated['decision_ids']))
        {
            $defender->decisions()->sync($validated['decision_ids']);
        }
        TagFieldService::syncTags($validated, $defender);
        if ($recursive)
        {
            $defender->decisions()->sync($decisionResult['ids']);
            $defender->groups()->sync($groupResult['ids']);
            $defender->tags()->sync($tagResult['ids']);
        }
        return [
            'status' => true,
            'id' => $defender->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    private static function generateGroup(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('group', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, GroupValidator::build());
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $relationships = [];
        if ($recursive)
        {
            $defenderResult = self::sync($data, $relationships, 'defenders', [self::class, 'generateDefender']);
            $ruleResult = self::sync($data, $relationships, 'rules', [self::class, 'generateRule']);
            $tagResult = self::sync($data, $relationships, 'tags', [self::class, 'generateTag']);
            if (!$defenderResult['status'] || !$ruleResult['status'] || !$tagResult['status'])
            {
                self::$failResource++;
                return [
                    'status' => false,
                    'id' => null,
                    'errors' => null,
                    'relationships' => $relationships,
                ];
            }
        }
        $group = Group::create($validated);
        if (isset($validated['defender_ids']))
        {
            $group->defenders()->sync($validated['defender_ids']);
        }
        if (isset($validated['rule_ids']))
        {
            $group->rules()->sync($validated['rule_ids']);
        }
        TagFieldService::syncTags($validated, $group);
        if ($recursive)
        {
            $group->defenders()->sync($defenderResult['ids']);
            $group->rules()->sync($ruleResult['ids']);
            $group->tags()->sync($tagResult['ids']);
        }
        return [
            'status' => true,
            'id' => $group->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    private static function generatePermission(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('permission', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, PermissionValidator::build());
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $relationships = [];
        if ($recursive)
        {
            $policyResult = self::sync($data, $relationships, 'policies', [self::class, 'generatePolicy']);
            $tagResult = self::sync($data, $relationships, 'tags', [self::class, 'generateTag']);
            if (!$policyResult['status'] || !$tagResult['status'])
            {
                self::$failResource++;
                return [
                    'status' => false,
                    'id' => null,
                    'errors' => null,
                    'relationships' => $relationships,
                ];
            }
        }
        $permission = Permission::create($validated);
        if (isset($validated['policy_ids']))
        {
            $permission->policies()->sync($validated['policy_ids']);
        }
        TagFieldService::syncTags($validated, $permission);
        if ($recursive)
        {
            $permission->policies()->sync($policyResult['ids']);
            $permission->tags()->sync($tagResult['ids']);
        }
        return [
            'status' => true,
            'id' => $permission->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    private static function generatePolicy(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('policy', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, PolicyValidator::build());
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $relationships = [];
        if ($recursive)
        {
            $permissionResult = self::sync($data, $relationships, 'permissions', [self::class, 'generatePermission']);
            $userResult = self::sync($data, $relationships, 'users', [self::class, 'generateUser']);
            $tagResult = self::sync($data, $relationships, 'tags', [self::class, 'generateTag']);
            if (!$permissionResult['status'] || !$userResult['status'] || !$tagResult['status'])
            {
                self::$failResource++;
                return [
                    'status' => false,
                    'id' => null,
                    'errors' => null,
                    'relationships' => $relationships,
                ];
            }
        }
        $policy = Policy::create($validated);
        if (isset($validated['permission_ids']))
        {
            $policy->permissions()->sync($validated['permission_ids']);
        }
        if (isset($validated['user_ids']))
        {
            $policy->users()->sync($validated['user_ids']);
        }
        TagFieldService::syncTags($validated, $policy);
        if ($recursive)
        {
            $policy->permissions()->sync($permissionResult['ids']);
            $policy->users()->sync($userResult['ids']);
            $policy->tags()->sync($tagResult['ids']);
        }
        return [
            'status' => true,
            'id' => $policy->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    private static function generateRule(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('rule', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, RuleValidator::build($data));
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        if ($validated['comparator'] == '@inRange')
        {
            $validated['value'] = implode(',', [$validated['from'], $validated['to']]);
        }
        if ($validated['action'])
        {
            $validated['action_configuration'] = match ($validated['action'])
            {
                'request' => implode(',', [$validated['request_method'], $validated['request_url']]),
                'setScore' => $validated['score'],
                'setLevel' => $validated['level'],
                'setVariable' => implode(',', [$validated['variable_key'], $validated['variable_value']]),
                'setHeader' => implode(',', [$validated['header_key'], $validated['header_value']]),
                default => $validated['action_configuration'] ?? null,
            };
        }
        $relationships = [];
        if ($recursive)
        {
            if (isset($data['wordlist']) && $data['wordlist'] != null)
            {
                if (isset($relationships['wordlist']))
                {
                    $relationships['wordlist'] = [];
                }
                $result = self::generateWordlist($data['wordlist'], false);
                $relationships['wordlist'][] = $result;
                if (!$result['status'])
                {
                    self::$failResource++;
                    return [
                        'status' => false,
                        'id' => null,
                        'errors' => null,
                        'relationships' => $relationships,
                    ];
                }
                if ($result['id'] != null)
                {
                    $validated['wordlist_id'] = $result['id'];
                }
            }
            $groupResult = self::sync($data, $relationships, 'groups', [self::class, 'generateGroup']);
            $tagResult = self::sync($data, $relationships, 'tags', [self::class, 'generateTag']);
            if (!$groupResult['status'] || !$tagResult['status'])
            {
                self::$failResource++;
                return [
                    'status' => false,
                    'id' => null,
                    'errors' => null,
                    'relationships' => $relationships,
                ];
            }
        }
        $rule = Rule::create($validated);
        if (isset($validated['group_ids']))
        {
            $rule->groups()->sync($validated['group_ids']);
        }
        TagFieldService::syncTags($validated, $rule);
        if ($recursive)
        {
            $rule->groups()->sync($groupResult);
            $rule->tags()->sync($tagResult);
        }
        return [
            'status' => true,
            'id' => $rule->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    private static function generateTag(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('tag', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, TagValidator::build());
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $tag = Tag::create($validated);
        return [
            'status' => true,
            'id' => $tag->id,
            'errors' => null,
            'relationships' => null,
        ];
    }

    private static function generateTarget(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('target', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, TargetValidator::build($data));
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $validated['final_datatype'] = match ($validated['engine'])
        {
            'indexOf' => 'string',
            'length' => 'number',
            default => $validated['datatype'],
        };
        $validated['engine_configuration'] = match ($validated['engine'])
        {
            'indexOf' => $validated['indexOf'],
            'addition',
            'subtraction',
            'multiplication',
            'division',
            'powerOf',
            'remainder' => $validated['number'],
            'hash' => $validated['hash'],
            default => null,
        };
        if ($validated['type'] == 'target')
        {
            $target = Target::find($validated['superior']);
            $validated['datatype'] = $target->final_datatype;
            $validated['name'] = $target->type . '_' . $target->name . '_' . now()->timestamp;
            $validated['target_id'] = $validated['superior'];
        }
        $relationships = [];
        if ($recursive)
        {
            if (isset($data['wordlist']) && $data['wordlist'] != null)
            {
                if (isset($relationships['wordlist']))
                {
                    $relationships['wordlist'] = [];
                }
                $result = self::generateWordlist($data['wordlist'], false);
                $relationships['wordlist'][] = $result;
                if (!$result['status'])
                {
                    self::$failResource++;
                    return [
                        'status' => false,
                        'id' => null,
                        'errors' => null,
                        'relationships' => $relationships,
                    ];
                }
                if ($result['id'] != null)
                {
                    $validated['wordlist_id'] = $result['id'];
                }
            }
            $tagResult = self::sync($data, $relationships, 'tags', [self::class, 'generateTag']);
            if (!$tagResult['status'])
            {
                self::$failResource++;
                return [
                    'status' => false,
                    'id' => null,
                    'errors' => null,
                    'relationships' => $relationships,
                ];
            }
        }
        $target = Target::create($validated);
        TagFieldService::syncTags($validated, $target);
        if ($recursive)
        {
            $target->tags()->sync($tagResult['ids']);
        }
        return [
            'status' => true,
            'id' => $target->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    private static function generateUser(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('user', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, UserValidator::build());
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $relationships = [];
        if ($recursive)
        {
            $policyResult = self::sync($data, $relationships, 'policies', [self::class, 'generatePolicy']);
            $tagResult = self::sync($data, $relationships, 'tags', [self::class, 'generateTag']);
            if (!$policyResult['status'] || !$tagResult['status'])
            {
                self::$failResource++;
                return [
                    'status' => false,
                    'id' => null,
                    'errors' => null,
                    'relationships' => $relationships,
                ];
            }
        }
        if ($validated['verification'])
        {
            $validated['token'] = Str::uuid();
            try
            {
                Mail::to($validated['email'])->send(
                    new VerificationMail(
                        $validated['name'],
                        $validated['token'],
                    ),
                );
            }
            catch (Exception $_)
            {
                $validated['email_verified_at'] = now();
            }
        }
        else
        {
            $validated['email_verified_at'] = now();
        }
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        if (isset($validated['policy_ids']))
        {
            $user->policies()->sync($validated['policy_ids']);
        }
        if (isset($validated['token_ids']))
        {
            $user->tokens()->sync($validated['policy_ids']);
        }
        TagFieldService::syncTags($validated, $user);
        if ($recursive)
        {
            $user->policies()->sync($policyResult['ids']);
            $user->policies()->sync($tagResult['ids']);
        }
        return [
            'status' => true,
            'id' => $user->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    private static function generateWordlist(array $data, bool $recursive = true)
    {
        self::$totalResource++;
        if (!self::bypass('wordlist', 'create'))
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, WordlistValidator::build());
        if ($validator->fails())
        {
            self::$failResource++;
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $relationships = [];
        if ($recursive)
        {
            $tagResult = self::sync($data, $relationships, 'tags', [self::class, 'generateTag']);
            if (!$tagResult['status'])
            {
                self::$failResource++;
                return [
                    'status' => false,
                    'id' => null,
                    'errors' => null,
                    'relationships' => $relationships,
                ];
            }
        }
        $wordlist = Wordlist::create($validated);
        if (isset($validated['words']))
        {
            $words = array_chunk($validated['words'], 10000);
            $now = now();
            foreach ($words as $content)
            {
                $records = [];
                foreach ($content as $line)
                {
                    $records[] = [
                        'content' => $line,
                        'wordlist_id' => $wordlist->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                Word::query()->insert($records);
            }
        }
        TagFieldService::syncTags($validated, $wordlist);
        if ($recursive)
        {
            $wordlist->tags()->sync($tagResult['ids']);
        }
        return [
            'status' => true,
            'id' => $wordlist->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }
}
