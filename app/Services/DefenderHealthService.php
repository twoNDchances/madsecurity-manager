<?php

namespace App\Services;

use App\Models\Defender;

class DefenderHealthService extends DefenderPreActionService
{
    public static function perform(Defender $record): Defender
    {
        $response = match ($record->protection)
        {
            true => HttpRequestService::perform(
                $record->health_method,
                "$record->url$record->health",
                null,
                true,
                $record->username,
                $record->password,
                $record->certification ? storage_path("app/$record->certification") : null
            ),
            false => HttpRequestService::perform(
                $record->health_method,
                "$record->url$record->health",
                null,
                true,
                null,
                null,
                $record->certification ? storage_path("app/$record->certification") : null
            ),
        };
        $lastStatus = false;

        $output = null;
        if (is_string($response))
        {
            $output = DefenderConsoleService::warning('health', $response);
        }
        else
        {
            $body = 'Status Code: ' . $response->status() . ' | Body: ' . $response->body();
            if ($response->successful())
            {
                $output = DefenderConsoleService::notice('health', $body);
                $lastStatus = true;
            }
            else
            {
                $output = DefenderConsoleService::warning('health', $body);
            }
        }
        DefenderConsoleService::updateOutput(
            $record,
            $output,
            ['last_status' => $lastStatus],
        );
        $status = null;
        if (!$lastStatus)
        {
            $status = 'failure';
        }
        NotificationService::announce($status, 'Health checked', $output);
        return $record;
    }
}
