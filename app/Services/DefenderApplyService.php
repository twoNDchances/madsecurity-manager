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

    private static string $actionType = 'apply';

    private static string $actionName = 'Data Application';

    private static function detail($severity, $message, Defender $defender, $status)
    {
        $output = self::log($severity, self::$actionType, $message, $defender);
        NotificationService::announce($status, self::$actionName, $output);
    }

    public static function performAll(Defender $defender): Defender
    {
        $rules = self::getGroupsAndReturnRules($defender->groups, $defender);
        $targets = self::getRulesAndReturnTargets($rules, $defender);
        self::getTargetsAndReturnPoint($targets, $defender);
        // dd(self::$requestApiForm);
        return $defender;
    }

    public static function performEach($group, Defender $defender)
    {
        $rules = self::getGroupsAndReturnRules([$group], $defender);
        $condition = true;
        if (empty($rules))
        {
            $condition = false;
        }
        $targets = self::getRulesAndReturnTargets($rules, $defender);
        if (empty($targets))
        {
            $condition = false;
        }
        if (!$condition)
        {
            $message = "Group [$group->id][$group->name] not applicable because no qualifying Rule exists";
            self::detail('warning', $message, $defender, 'warning');
            return;
        }
        $point = self::getTargetsAndReturnPoint($targets, $defender);
        if ($point == 0)
        {
            $message = "Group [$group->id][$group->name] not applicable because no qualifying Target exists";
            self::detail('warning', $message, $defender, 'warning');
            return;
        }
        $message = "Group [$group->id][$group->name] has been applied";
        self::detail('notice', $message, $defender, null);
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
}
