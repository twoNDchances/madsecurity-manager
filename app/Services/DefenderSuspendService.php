<?php

namespace App\Services;

use App\Models\Defender;
use App\Models\Wordlist;
use Illuminate\Support\Carbon;

class DefenderSuspendService extends DefenderPreActionService
{
    protected static array $requestApiForm = [
        'decisions' => [],
        'wordlists' => [],
        'words' => [],
    ];

    protected static ?string $actionType = 'suspend';

    protected static ?string $actionName = 'Data Suspension';

    public static function performAll(Defender $defender, $notify = true): Defender
    {
        self::getDecisions($defender->decisions, $defender);
        self::generalAction(
            'Defender',
            $defender->id,
            $defender->name,
            $defender,
            $notify,
        );
        return $defender;
    }

    public static function performEach($decision, Defender $defender, $notify = true): Defender
    {
        self::getDecisions([$decision], $defender);
        self::generalAction(
            'Decision',
            $decision->id,
            $decision->name,
            $defender,
            $notify,
        );
        return $defender;
    }

    private static function generalAction($type, $id, $name, $defender, $notify = true)
    {
        if (empty(self::$requestApiForm['decisions']))
        {
            $message = "$type [$id][$name] not suspendable because no qualifying Decision exists";
            self::detail('warning', $message, $defender, 'warning');
            return;
        }
        $result = self::send(
            $defender,
            $defender->suspend_method,
            "$defender->url$defender->suspend",
            false,
            $notify,
        );
        if ($result['status'])
        {
            $message = "$type [$id][$name] has been suspended";
            self::detail('notice', $message, $defender, null);
            foreach ($result['successIds'] as $groupId)
            {
                $defender->decisions()->updateExistingPivot($groupId, ['status' => false, 'updated_at' => Carbon::now()]);
            }
        }
    }

    private static function getDecisions($decisions, Defender $defender)
    {
        foreach ($decisions as $decision)
        {
            $context = "Decision [$decision->id][$decision->name]";
            if (in_array($decision->action, ['tag', 'warn']) && !$decision->wordlist_id)
            {
                $message = "$context missing Wordlist";
                self::detail('emergency', $message, $defender, 'failure');
                continue;
            }
            if ($decision->wordlist_id)
            {
                $success = self::getWordlistWithItsWordsAndReturnBoolean(
                    $context,
                    $decision->wordlist_id,
                    $defender,
                );
                if (!$success)
                {
                    continue;
                }
            }
            self::$requestApiForm['decisions'][] = $decision->id;
        }
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
