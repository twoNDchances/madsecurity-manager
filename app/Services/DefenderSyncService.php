<?php

namespace App\Services;

use App\Models\Defender;
use App\Models\Group;
use App\Models\Rule;
use App\Models\Target;
use App\Models\Word;
use App\Models\Wordlist;
use Illuminate\Support\Str;

class DefenderSyncService extends DefenderPreActionService
{
    protected static ?string $actionType = 'sync';

    protected static ?string $actionName = 'Data Synchronization';

    private static $data = [];

    public static function perform(Defender $defender): Defender
    {
        $response = self::fetch($defender);
        if ($response["status"] != 200)
        {
            self::detail('danger', $response['message'], $defender, 'failure');
        }
        else
        {
            $groups = self::syncGroups($defender);
            self::syncRules($groups);

            $message = 'Status Code: 200 | Body: ' . json_encode(self::$data['counters']);
            self::detail('notice', $message, $defender, 'success');
            NotificationService::notify(null, static::$actionName, $message);
        }
        return $defender;
    }

    private static function syncGroups(Defender $defender): array
    {
        $groupModels = [];
        foreach (self::$data['groups'] as $group)
        {
            $oldGroup = Group::find($group['id']);
            if (!$oldGroup)
            {
                $newGroup = Group::create([
                    'id' => $group['id'],
                    'execution_order' => $group['execution_order'],
                    'level' => $group['level'],
                    'name' => $group['name'] . '-synced-' . now()->timestamp,
                ]);
                $newGroup->defenders()->attach($defender->id);
                $newGroup->defenders()->updateExistingPivot(
                    $defender->id,
                    ['status' => true],
                );
                $groupModels[] = [
                    'groupId' => $group['id'],
                    'ruleIds' => $group['rules'],
                ];
            }
        }
        return $groupModels;
    }

    private static function syncRules($groups)
    {
        foreach ($groups as $group)
        {
            foreach (self::$data['rules'] as $rule)
            {
                if (!in_array($rule['id'], $group['ruleIds']))
                {
                    //
                    continue;
                }
                $targetObject = null;
                foreach (self::$data['targets'] as $target)
                {
                    if ($target['id'] == $rule['target_id'])
                    {
                        $targetObject = $target;
                        break;
                    }
                }
                if (!$targetObject)
                {
                    //
                    continue;
                }
                $targetId = self::syncTarget($targetObject);
                $oldRule = Rule::find($rule['id']);
                if (!$oldRule)
                {
                    $newRule = Rule::create([
                        'id' => $rule['id'],
                        'name' => $rule['name'],
                        'alias' => $rule['alias'] . '-synced-' . now()->timestamp,
                        'phase' => $rule['phase'],

                        'target_id' => $targetId,

                        'comparator' => $rule['comparator'],
                        'inverse' => $rule['inverse'],

                        'value' => $rule['value'],
                        'action' => $rule['action'],
                        'action_configuration' => $rule['action_configuration'],
                        'severity' => $rule['severity'],

                        'log' => $rule['log'],
                        'time' => $rule['time'],
                        'user_agent' => $rule['user_agent'],
                        'client_ip' => $rule['client_ip'],
                        'method' => $rule['method'],
                        'path' => $rule['path'],
                        'output' => $rule['output'],
                        'target' => $rule['target'],
                        'rule' => $rule['rule'],
                    ]);
                    $newRule->groups()->attach($group['groupId']);
                    if ($rule['wordlist_id'])
                    {
                        $wordlistObject = null;
                        foreach (self::$data['wordlists'] as $wordlist)
                        {
                            if ($wordlist['id'] == $rule['wordlist_id'])
                            {
                                $wordlistObject = $wordlist;
                                break;
                            }
                        }
                        if (!$wordlistObject)
                        {
                            //
                            continue;
                        }
                        $wordlistId = self::syncWordlist($wordlistObject);
                        $newRule->update([
                            'wordlist_id' => $wordlistId,
                        ]);
                    }
                }
            }
        }
    }

    private static function syncTarget($target): int
    {
        $oldTarget = Target::find($target['id']);
        if (!$oldTarget)
        {
            if ($target['target_id'])
            {
                
            }
            else
            {
                $newTarget = Target::create([
                    'name' => $target['name'],
                    'alias' => $target['alias'] . '-synced-' . now()->timestamp,
                    'type' => $target['type'],
                    'engine' => $target['engine'],
                    'engine_configuration' => $target['engine_configuration'],
                    'phase' => $target['phase'],
                    'datatype' => $target['datatype'],
                    'final_datatype' => $target['final_datatype'],
                    'immutable' => $target['immutable'],
                    'target_id' => null,
                ]);
                if ($target['wordlist_id'])
                {
                    $wordlistObject = null;
                    foreach (self::$data['wordlists'] as $wordlist)
                    {
                        if ($wordlist['id'] == $target['wordlist_id'])
                        {
                            $wordlistObject = $wordlist;
                            break;
                        }
                    }
                    if (!$wordlistObject)
                    {
                        //
                    }
                    else
                    {
                        $wordlistId = self::syncWordlist($wordlistObject);
                        $newTarget->update([
                            'wordlist_id' => $wordlistId,
                        ]);
                    }
                }
            }
        }
        return $target['id'];
    }

    private static function syncWordlist($wordlist): int
    {
        $oldWordlist = Wordlist::find($wordlist['id']);
        if (!$oldWordlist)
        {
            Wordlist::create([
                'id' => $wordlist['id'],
                'name' => $wordlist['name'],
                'alias' => $wordlist['alias'] . '-synced-' . now()->timestamp,
            ]);
            $words = [];
            foreach (self::$data['words'] as $word)
            {
                if ($word['wordlist_id'] == $wordlist['id'])
                {
                    $words[] = $word['content'];
                }
            }
            $now = now();
            $chunked = array_chunk($words, 10000);
            foreach ($chunked as $content)
            {
                $records = [];
                foreach ($content as $line)
                {
                    $records[] = [
                        'content' => $line,
                        'wordlist_id' => $wordlist['id'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                Word::insert($records);
            }
        }
        return $wordlist['id'];
    }

    private static function syncDecisions($decisions, Defender $defender)
    {

    }

    private static function fetch(Defender $defender): array
    {
        $baseUrl = "$defender->url$defender->sync";
        $authUser = $defender->protection ? $defender->username : null;
        $authPass = $defender->protection ? $defender->password : null;
        $request = HttpRequestService::perform(
            'post',
            "$baseUrl?page=1&pageSize=10000",
            null,
            false,
            $authUser,
            $authPass,
        );
        if (is_string($request)) {
            $response = explode(' | ', $request);
            return [
                'status' => 500,
                'message' => count($response) == 2 ? Str::replaceFirst('Body: ', '', $response[1]) : 'Unknown',
            ];
        }
        $response = $request->json('data');
        self::$data = [
            'counters' => $response['counters'],
            'decisions' => $response['resources']['decisions'] ?? [],
            'groups' => $response['resources']['groups'] ?? [],
            'rules' => $response['resources']['rules'] ?? [],
            'targets' => $response['resources']['targets'] ?? [],
            'wordlists' => $response['resources']['wordlists'] ?? [],
            'words' => $response['resources']['words'] ?? [],
        ];
        $page = 2;
        $total = max($response['pages']['totalPages']) - 1;
        for ($_ = 0; $_ < $total; $_++)
        {
            $nextRequest = HttpRequestService::perform(
                'post',
                "$baseUrl?page=$page&pageSize=10000",
                null,
                false,
                $authUser,
                $authPass,
            );
            if (is_string($nextRequest))
            {
                continue;
            }
            if ($nextRequest->status() != 200)
            {
                continue;
            }
            $response = $nextRequest->json('data');
            foreach ($response['resources'] as $resourceName => $resourceData)
            {
                self::$data[$resourceName] = array_merge(self::$data[$resourceName], $resourceData);
            }
            $page++;
        }
        return [
            'status' => $request->status(),
            'message' => $request->json('message'),
        ];
    }
}
