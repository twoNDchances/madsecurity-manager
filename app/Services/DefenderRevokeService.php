<?php

namespace App\Services;

use App\Models\Defender;
use App\Models\Group;
use App\Models\Target;
use App\Models\Wordlist;

class DefenderRevokeService extends DefenderPreActionService
{
    protected static array $requestApiForm = [
        'groups' => [],
        'rules' => [],
        'targets' => [],
        'wordlists' => [],
        'words' => [],
    ];

    protected static ?string $actionType = 'revoke';

    protected static ?string $actionName = 'Data Revocation';

    public static function performAll(Defender $defender): Defender
    {
        $rules = self::getGroupsAndReturnRules($defender->groups, $defender);
        self::generalAction('Defender', $defender->id, $defender->name, $rules, $defender);
        return $defender;
    }

    public static function performEach(Group $group, Defender $defender)
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
        $result = self::send($defender, $defender->revoke_method, "$defender->url$defender->revoke");
        if ($result)
        {
            $message = "$type [$id][$name] has been revoked";
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
            self::$requestApiForm['groups'][] = $group->id;
        }
        return $rules;
    }

    private static function getRulesAndReturnTargets(array $rules, Defender $defender): array
    {
        $targets = [];
        foreach ($rules as $rule)
        {
            $context = "Rule [" . $rule['id'] . "][". $rule['alias'] . "]";
            if (!$rule['target_id'])
            {
                $message = "$context missing Target";
                self::detail('emergency', $message, $defender, 'failure');
                continue;
            }
            else if (in_array($rule['comparator'], ['@check', '@checkRegex']) && !$rule['wordlist_id'])
            {
                $message = "$context missing Wordlist";
                self::detail('emergency', $message, $defender, 'failure');
                continue;
            }
            $targets[] = $rule['target_id'];
            if ($rule['wordlist_id'])
            {
                $success = self::getWordlistWithItsWordsAndReturnBoolean(
                    $context,
                    $rule['wordlist_id'],
                    $defender
                );
                if (!$success)
                {
                    continue;
                }
            }
            self::$requestApiForm['rules'][] = $rule['id'];
        }
        $targets = Target::whereIn('id', $targets)->get()->toArray();
        return $targets;
    }

    private static function getTargetsAndReturnPoint(array $targets, Defender $defender): int
    {
        $point = 0;
        foreach ($targets as $target)
        {
            $context = "Target [" . $target['id'] . "][". $target['alias'] . "]";
            if ($target['datatype'] == 'array' && !$target['wordlist_id'] && $target['type'] != 'target' && !$target['immutable'])
            {
                $message = "$context missing Wordlist";
                self::detail('emergency', $message, $defender, 'failure');
                continue;
            }
            if ($target['wordlist_id'])
            {
                $success = self::getWordlistWithItsWordsAndReturnBoolean(
                    $context,
                    $target['wordlist_id'],
                    $defender
                );
                if (!$success)
                {
                    continue;
                }
            }
            self::$requestApiForm['targets'][] = $target['id'];
            $point++;
        }
        return $point;
    }

    private static function getWordlistWithItsWordsAndReturnBoolean($context, $wordlistId, Defender $defender): bool
    {
        $wordlist = Wordlist::find($wordlistId);
        if (!$wordlist)
        {
            $message = "$context : Wordlist [$wordlistId] not found";
            self::detail('emergency', $message, $defender, 'failure');
            return false;
        }
        $words = $wordlist->words()->get()->toArray();
        foreach ($words as $word) {
            self::$requestApiForm['words'][] = $word['id'];
        }
        self::$requestApiForm['wordlists'][] = $wordlistId;
        return true;
    }
}
