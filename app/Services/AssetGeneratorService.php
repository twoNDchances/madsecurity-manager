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
use App\Models\Token;
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
use App\Validators\API\TokenValidator;
use App\Validators\API\UserValidator;
use App\Validators\API\WordlistValidator;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AssetGeneratorService
{
    private static function bypass($resource, $action)
    {
        return IdentificationService::can(
            IdentificationService::get(),
            $resource,
            $action,
        );
    }

    public static function generateDecision(array $data)
    {
        if (!self::bypass('decision', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, DecisionValidator::build($data));
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
            ];
        }
        $validated = $validator->validated();
        if ($validated['action'] == 'kill')
        {
            $validated['action_configuration'] = implode(',', [$validated['kill_header'], $validated['kill_path']]);
        }
        $decision = Decision::create($validated);
        if (isset($validated['defender_ids']))
        {
            $decision->defenders()->sync($validated['defender_ids']);
        }
        TagFieldService::syncTags($validated, $decision);
        return [
            'status' => true,
            'id' => $decision->id,
            'errors' => null,
        ];
    }

    public static function generateDefender(array $data)
    {
        if (!self::bypass('defender', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, DefenderValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
            ];
        }
        $validated = $validator->validated();
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
        return [
            'status' => true,
            'id' => $defender->id,
            'errors' => null,
        ];
    }

    public static function generateGroup(array $data)
    {
        if (!self::bypass('group', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, GroupValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
            ];
        }
        $validated = $validator->validated();
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
        return [
            'status' => true,
            'id' => $group->id,
            'errors' => null,
        ];
    }

    public static function generatePermission(array $data)
    {
        if (!self::bypass('permission', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, PermissionValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
            ];
        }
        $validated = $validator->validated();
        $permission = Permission::create($validated);
        if (isset($validated['policy_ids']))
        {
            $permission->policies()->sync($validated['policy_ids']);
        }
        TagFieldService::syncTags($validated, $permission);
        return [
            'status' => true,
            'id' => $permission->id,
            'errors' => null,
        ];
    }

    public static function generatePolicy(array $data)
    {
        if (!self::bypass('policy', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, PolicyValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
            ];
        }
        $validated = $validator->validated();
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
        return [
            'status' => true,
            'id' => $policy->id,
            'errors' => null,
        ];
    }

    public static function generateRule(array $data)
    {
        if (!self::bypass('rule', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, RuleValidator::build($data));
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
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
                'setVariable' => implode(',', [$validated['variable_key'], $validated['variable_value']]),
                'setHeader' => implode(',', [$validated['header_key'], $validated['header_value']]),
                default => $validated['action_configuration'] ?? null,
            };
        }
        $rule = Rule::create($validated);
        if (isset($validated['group_ids']))
        {
            $rule->groups()->sync($validated['group_ids']);
        }
        TagFieldService::syncTags($validated, $rule);
        return [
            'status' => true,
            'id' => $rule->id,
            'errors' => null,
        ];
    }

    public static function generateTag(array $data)
    {
        if (!self::bypass('tag', 'create'))
        {
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

    public static function generateTarget(array $data)
    {
        if (!self::bypass('target', 'create'))
        {
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
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $validated['final_datatype'] = match ($validated['engine']) {
            'indexOf' => 'string',
            'length' => 'number',
            default => $validated['datatype'],
        };
        $target = Target::create($validated);
        TagFieldService::syncTags($validated, $target);
        $relationships = [];
        if (isset($data['tags']))
        {
            foreach ($data['tags'] as $tag)
            {
                $relationships['tags'][] = self::generateTag($tag);
            }
        }
        if (isset($data['wordlist']))
        {
            $relationships['wordlist'][] = self::generateWordlist($data['wordlist']);
        }
        return [
            'status' => true,
            'id' => $target->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    public static function generateToken(array $data)
    {
        if (!self::bypass('user', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
                'relationships' => null,
            ];
        }
        $validator = Validator::make($data, TokenValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
        $value = null;
        while (true)
        {
            $value = Str::random(48);
            $alreadyExists = false;
            Token::cursor()->each(function ($token) use (&$alreadyExists, $value)
            {
                if (Hash::check($value, $token->value))
                {
                    $alreadyExists = true;
                    return false;
                }
            });
            if (!$alreadyExists)
            {
                break;
            }
        }
        $validated['value'] = Hash::make($value);
        $token = Token::create($validated);
        if (isset($validated['user_ids']))
        {
            $token->users()->sync($validated['user_ids']);
        }
        TagFieldService::syncTags($validated, $token);
        $relationships = [];
        if (isset($data['tags']))
        {
            foreach ($data['tags'] as $tag)
            {
                $relationships['tags'][] = self::generateTag($tag);
            }
        }
        if (isset($data['users']))
        {
            foreach ($data['users'] as $user)
            {
                $relationships['users'][] = self::generateUser($user);
            }
        }
        return [
            'status' => true,
            'id' => $token->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    public static function generateUser(array $data)
    {
        if (!self::bypass('user', 'create'))
        {
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
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
                'relationships' => null,
            ];
        }
        $validated = $validator->validated();
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
        $relationships = [];
        if (isset($data['policies']))
        {
            foreach ($data['policies'] as $policy)
            {
                $relationships['policies'][] = self::generatePolicy($policy);
            }
            $policyIds = array_values(array_filter(
                $relationships['policies'],
                fn($item) => $item['id'] != null,
            ));
            $user->policies()->sync($policyIds);
        }
        if (isset($data['tags']))
        {
            foreach ($data['tags'] as $tag)
            {
                $relationships['tags'][] = self::generateTag($tag);
            }
        }
        if (isset($data['tokens']))
        {
            foreach ($data['tokens'] as $token)
            {
                $relationships['tokens'][] = self::generateToken($token);
            }
        }
        return [
            'status' => true,
            'id' => $user->id,
            'errors' => null,
            'relationships' => $relationships,
        ];
    }

    public static function generateWordlist(array $data): array
    {
        if (!self::bypass('wordlist', 'create'))
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => [
                    'permission' => 'action denied',
                ],
            ];
        }
        $validator = Validator::make($data, WordlistValidator::build());
        if ($validator->fails())
        {
            return [
                'status' => false,
                'id' => null,
                'errors' => $validator->errors()->toArray(),
            ];
        }
        $validated = $validator->validated();
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
                Word::insert($records);
            }
        }
        TagFieldService::syncTags($validated, $wordlist);
        return [
            'status' => true,
            'id' => $wordlist->id,
            'errors' => null,
        ];
    }
}
