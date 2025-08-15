<?php

namespace App\Services;

use App\Models\Decision;
use App\Models\Defender;
use App\Models\Wordlist;
use Illuminate\Support\Carbon;

class DefenderImplementService extends DefenderPreActionService
{
    protected static array $requestApiForm = [
        'decisions' => [],
        'wordlists' => [],
        'words' => [],
    ];

    protected static ?string $actionType = 'implement';

    protected static ?string $actionName = 'Data Implementation';

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

    public static function performSpecific($decisions, Defender $defender, $notify = true)
    {
        self::getDecisions($decisions, $defender);
        self::generalAction(
            'Defender',
            $defender->id,
            $defender->name,
            $defender,
            $notify,
        );
        return $defender;
    }

    public static function performEach(Decision $decision, Defender $defender, $notify = true)
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

    private static function generalAction($type, $id, $name, Defender $defender, $notify = true)
    {
        if (empty(self::$requestApiForm['decisions']))
        {
            $message = "$type [$id][$name] not implementable because no qualifying Decision exists";
            self::detail('warning', $message, $defender, 'warning');
            return;
        }
        $result = self::send(
            $defender,
            $defender->implement_method,
            "$defender->url$defender->implement",
            false,
            $notify,
        );
        if ($result['status'])
        {
            $message = "$type [$id][$name] has been implemented";
            self::detail('notice', $message, $defender, null);
            foreach ($result['successIds'] as $groupId)
            {
                $defender->decisions()->updateExistingPivot($groupId, ['status' => true, 'updated_at' => Carbon::now()]);
            }
        }
    }

    private static function getDecisions($decisions, Defender $defender)
    {
        foreach ($decisions as $decision)
        {
            if (in_array($decision->action, ['tag', 'warn']) && !$decision->wordlist_id)
            {
                $message = "Decision [$decision->id][$decision->name] missing Wordlist";
                self::detail('emergency', $message, $defender, 'failure');
                continue;
            }
            if ($decision->wordlist_id)
            {
                self::getWordlistAndItsWords($decision->wordlist_id, $defender);
            }
            self::$requestApiForm['decisions'][] = [
                'id' => $decision->id,
                'name' => $decision->name,
                'phase_type' => $decision->phase_type,
                'score' => $decision->score,
                'action' => $decision->action,
                'action_configuration' => $decision->action_configuration,
                'wordlist_id' => $decision->wordlist_id,
            ];
        }
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
        $words = $wordlist->words()->get()->each->makeVisible('wordlist_id')->toArray();
        foreach ($words as $word) {
            self::$requestApiForm['words'][] = self::clean($word);
        }
        self::$requestApiForm['wordlists'][] = self::clean($wordlist->toArray());
    }
}
