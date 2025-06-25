<?php

namespace App\Services;

use App\Models\Defender;
use App\Models\Target;
use App\Models\Wordlist;

class DefenderApplyService extends DefenderPreActionService
{
    private static array $requestApiForm = [
        'groups' => [],
        'rules' => [],
        'targets' => [],
        'wordlists' => [],
        'words' => [],
    ];

    protected static ?string $actionType = 'apply';

    protected static ?string $actionName = 'Data Application';

    public static function performAll(Defender $defender): Defender
    {
        $rules = self::getGroupsAndReturnRules($defender->groups, $defender);
        self::generalAction('Defender', $defender->id, $defender->name, $rules, $defender);
        return $defender;
    }

    public static function performEach($group, Defender $defender)
    {
        $rules = self::getGroupsAndReturnRules([$group], $defender);
        self::generalAction('Group', $group->id, $group->name, $rules, $defender);
    }

    private static function generalAction($type, $id, $name, $rules, $defender)
    {
        $targets = self::getRulesAndReturnTargets($rules, $defender);
        if (empty($rules) || empty($targets))
        {
            $message = "$type [$id][$name] not applicable because no qualifying Rule exists";
            self::detail('warning', $message, $defender, 'warning');
            return;
        }
        $point = self::getTargetsAndReturnPoint($targets, $defender);
        if ($point == 0)
        {
            $message = "$type [$id][$name] not applicable because no qualifying Target exists";
            self::detail('warning', $message, $defender, 'warning');
            return;
        }
        $result = self::send($defender);
        if ($result)
        {
            $message = "$type [$id][$name] has been applied";
            self::detail('notice', $message, $defender, null);
        }
    }

    private static function getGroupsAndReturnRules($groups, $defender): array
    {
        $rules = [];
        foreach ($groups as $group)
        {
            if (!$group->rules()->exists())
            {
                $message = "Group [$group->id][$group->name] does not have any Rules";
                self::detail('warning', $message, $defender, 'warning');
                continue;
            }
            foreach ($group->rules->toArray() as $rule)
            {
                $rules[] = $rule;
            }
            $ruleIds = $group->rules->pluck('id')->toArray();
            $group = self::clean(
                $group->toArray(),
                ['pivot'],
            );
            $group['rules'] = $ruleIds;
            $group['defender_id'] = $defender->id;
            self::$requestApiForm['groups'][] = $group;
        }
        return $rules;
    }

    private static function getRulesAndReturnTargets($rules, Defender $defender): array
    {
        $targets = [];
        foreach ($rules as $rule)
        {
            if (!$rule['target_id'])
            {
                $message = "Rule [" . $rule['id'] . "][". $rule['alias'] . "] missing Target";
                self::detail('emergency', $message, $defender, 'failure');
                continue;
            }
            else if (in_array($rule['comparator'], ['@check', '@checkRegex']) && !$rule['wordlist_id'])
            {
                $message = "Rule [" . $rule['id'] . "][". $rule['alias'] . "] missing Wordlist";
                self::detail('emergency', $message, $defender, 'failure');
                continue;
            }
            $targets[] = $rule['target_id'];
            if ($rule['wordlist_id'])
            {
                self::getWordlistAndItsWords($rule['wordlist_id'], $defender);
            }
            self::$requestApiForm['rules'][] = self::clean(
                $rule,
                ['pivot'],
            );
        }
        $targets = Target::whereIn('id', $targets)->get()->toArray();
        return $targets;
    }

    private static function getTargetsAndReturnPoint($targets, Defender $defender): int
    {
        $point = 0;
        foreach ($targets as $target)
        {
            if ($target['datatype'] == 'array' && !$target['wordlist_id'] && $target['type'] != 'target' && !$target['immutable'])
            {
                $message = "Target [" . $target['id'] . "][". $target['alias'] . "] missing Wordlist";
                self::detail('emergency', $message, $defender, 'failure');
                continue;
            }
            if ($target['wordlist_id'])
            {
                self::getWordlistAndItsWords($target['wordlist_id'], $defender);
            }
            self::$requestApiForm['targets'][] = self::clean(
                $target,
                ['immutable'],
            );
            $point++;
        }
        return $point;
    }

    private static function getWordlistAndItsWords($wordlistId, Defender $defender): void
    {
        $wordlist = Wordlist::find($wordlistId);
        if (!$wordlist)
        {
            $message = "Wordlist [$wordlistId] not found";
            self::detail('emergency', $message, $defender, 'failure');
            return;
        }
        $words = $wordlist->words()->get()->toArray();
        foreach ($words as $word) {
            self::$requestApiForm['words'][] = self::clean($word);
        }
        self::$requestApiForm['wordlists'][] = self::clean($wordlist->toArray());
    }

    private static function send(Defender $defender): bool
    {
        $batchMinSize = 10000;
        $batchMaxSize = 100000;
        foreach (self::$requestApiForm as $_ => $items) {
            if (count($items) > $batchMaxSize) {
                $batchMinSize = $batchMaxSize;
                break;
            }
        }
        $batches = array_map(
            fn($items) => array_chunk($items, $batchMinSize),
            self::$requestApiForm,
        );
        $result = [];
        $maxBatchCount = max(array_map('count', $batches));
        $status = [
            'pass' => 0,
            'fall' => 0,
        ];
        for ($i = 0; $i < $maxBatchCount; $i++) {
            $apiBatch = [
                'groups' => $batches['groups'][$i] ?? [],
                'rules' => $batches['rules'][$i] ?? [],
                'targets' => $batches['targets'][$i] ?? [],
                'wordlists' => $batches['wordlists'][$i] ?? [],
                'words' => $batches['words'][$i] ?? [],
            ];
            $response = HttpRequestService::perform(
                $defender->apply_method,
                "$defender->url$defender->apply",
                $apiBatch,
                false,
                $defender->protection ? $defender->username : null,
                $defender->protection ? $defender->password : null,
            );
            if (!is_string($response))
            {
                $message = implode(' | ', [
                    "Batch: $i",
                    'Status: ' . $response->status(),
                    'Body: ' . $response->body(),
                ]);
                $result[] = $message;
                if (!$response->successful())
                {
                    self::detail('danger', $message, $defender, 'failure');
                    $status['fall']++;
                }
                else
                {
                    self::detail('notice', $message, $defender, 'success');
                    $status['pass']++;
                }
            }
            else
            {
                $message = implode(' | ', [
                    "Batch: $i",
                    explode(' | ', $response)[0],
                    explode(' | ', $response)[1],
                ]);
                $result[] = $message;
                self::detail('danger', $message, $defender, 'failure');
                $status['fall']++;
            }
        }
        NotificationService::notify(null, self::$actionName, implode("\n", $result));
        return match ($status['pass'] > 0)
        {
            true => true,
            false => false,
        };
    }
}
