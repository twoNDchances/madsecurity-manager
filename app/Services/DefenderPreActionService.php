<?php

namespace App\Services;

use App\Models\Defender;

class DefenderPreActionService
{
    protected static function clean($data, $more = [])
    {
        $unnecessaries = [
            'description',
            'user_id',
            'created_at',
            'updated_at',
        ];
        if (!empty($more))
        {
            $unnecessaries = array_merge($unnecessaries, $more);
        }
        foreach ($unnecessaries as $unnecessary)
        {
            unset($data[$unnecessary]);
        }
        return $data;
    }

    protected static function log($severity, $action, $message, Defender $defender): string
    {
        $output = match ($severity)
        {
            'notice' => DefenderConsoleService::notice($action, $message),
            'warning' => DefenderConsoleService::warning($action, $message),
            'danger' => DefenderConsoleService::danger($action, $message),
            'emergency' => DefenderConsoleService::emergency($action, $message),
        };
        DefenderConsoleService::updateOutput($defender, $output);
        return $output;
    }
}
