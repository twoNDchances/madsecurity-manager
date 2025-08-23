<?php

namespace App\Services;

use App\Models\Defender;

class DefenderHealthService extends DefenderPreActionService
{
    public static function perform(Defender $defender, $notify = true): Defender
    {
        $response = self::request($defender, $notify);
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
            $defender,
            $output,
            ['last_status' => $lastStatus],
        );
        $status = null;
        if (!$lastStatus)
        {
            $status = 'failure';
        }
        NotificationService::announce($status, 'Health checked', $output);
        return $defender;
    }
}
