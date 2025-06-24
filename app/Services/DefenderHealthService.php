<?php

namespace App\Services;

use App\Models\Defender;

class DefenderHealthService
{
    public static function perform(Defender $record): Defender
    {
        $response = match ($record->protection)
        {
            true => HttpRequestService::perform(
                'get',
                "$record->url$record->health",
                null,
                true,
                $record->username,
                $record->password,
            ),
            false => HttpRequestService::perform(
                'get',
                "$record->url$record->health"
            ),
        };
        $lastStatus = false;

        $output = null;
        if (is_string($response))
        {
            $output = DefenderConsoleService::warning($response);
        }
        else
        {
            $body = 'Status Code: ' . $response->status() . ' | Body: ' . $response->body();
            if ($response->successful())
            {
                $output = DefenderConsoleService::notice($body);
                $lastStatus = true;
            }
            else
            {
                $output = DefenderConsoleService::warning($body);
            }
        }
        $newOutput = $record->output;
        $newOutput[] = $output;
        $record->update([
            'lastStatus' => $lastStatus,
            'output' => $newOutput,
        ]);
        NotificationService::announce(null, 'Health checked', $output);
        return $record;
    }
}
