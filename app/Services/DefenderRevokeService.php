<?php

namespace App\Services;

use App\Models\Defender;
use App\Models\Group;
use App\Models\Target;
use App\Models\Wordlist;
use Carbon\Carbon;

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

    public static function performAll(Defender $defender, $notify = true): Defender
    {
        $rules = self::getGroupsAndReturnRules($defender->groups, $defender);
        self::generalAction(
            'Defender',
            $defender->id,
            $defender->name,
            $defender,
            $rules,
            $notify,
        );
        return $defender;
    }

    public static function performSpecific($groups, Defender $defender, $notify = true): Defender
    {
        $rules = self::getGroupsAndReturnRules($groups, $defender);
        self::generalAction(
            'Defender',
            $defender->id,
            $defender->name,
            $defender,
            $rules,
            $notify,
        );
        return $defender;
    }

    public static function performEach(Group $group, Defender $defender, $notify = true): Defender
    {
        $rules = self::getGroupsAndReturnRules([$group], $defender);
        self::generalAction(
            'Group',
            $group->id,
            $group->name,
            $defender,
            $rules,
            $notify,
        );
        return $defender;
    }

    private static function generalAction($type, $id, $name, $defender, $rules, $notify = true)
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
        $result = self::send(
            $defender,
            $defender->revoke_method,
            "$defender->url$defender->revoke",
            true,
            $notify,
        );
        if ($result['status'])
        {
            $message = "$type [$id][$name] has been revoked";
            self::detail('notice', $message, $defender, null);
            foreach ($result['successIds'] as $groupId)
            {
                $defender->groups()->updateExistingPivot($groupId, ['status' => false, 'updated_at' => Carbon::now()]);
            }
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
            foreach ($group->rules->each->makeVisible(['target_id', 'wordlist_id'])->toArray() as $rule)
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
        $targets = Target::whereIn('id', $targets)->get()->each->makeVisible('wordlist_id')->toArray();
        return $targets;
    }

    private static function getAllToRoot($targetId, &$visited = []): array
    {
        if (in_array($targetId, $visited)) {
            return [];
        }
        $target = Target::find($targetId)->makeVisible('wordlist_id');
        if (!$target) {
            return [];
        }
        $visited[] = $targetId;
        $chain = [$target];
        if ($target->target_id) {
            $chain = array_merge($chain, self::getAllToRoot($target->target_id, $visited));
        }
        return $chain;
    }

    private static function getTargetsAndReturnPoint(array $targets, Defender $defender): int
    {
        $point = 0;
        $targetReferers = [];
        foreach ($targets as $target)
        {
            $context = "Target [" . $target['id'] . "][". $target['alias'] . "]";
            if ($target['datatype'] == 'array' && !$target['wordlist_id'] && $target['type'] != 'target' && !$target['immutable'])
            {
                $message = "$context missing Wordlist";
                self::detail('emergency', $message, $defender, 'failure');
                continue;
            }
            $targetReferers = self::getAllToRoot($target['id']);
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
        if (count($targetReferers) > 0)
        {
            foreach ($targetReferers as $targetReferer)
            {
                $target = $targetReferer->toArray();
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
        $words = $wordlist->words()->get()->each->makeVisible('wordlist_id')->toArray();
        foreach ($words as $word) {
            self::$requestApiForm['words'][] = $word['id'];
        }
        self::$requestApiForm['wordlists'][] = $wordlistId;
        return true;
    }
}
