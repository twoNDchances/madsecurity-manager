<?php

namespace App\Services;

use App\Models\Defender;
use Carbon\Carbon;

class DefenderConsoleService
{
    private static array $severities = [
        'notice'    => 'NOTICE',
        'warning'   => 'WARNING',
        'danger'    => 'DANGER',
        'emergency' => 'EMERGENCY',
    ];

    private static array $actions = [
        'health'    => 'HEALTH',
        'apply'     => 'APPLY',
        'revoke'    => 'REVOKE',
        'implement' => 'IMPLEMENT',
        'suspend'   => 'SUSPEND',
    ];

    private static function build($severity, $action, $message): string
    {
        $time = Carbon::now()->format('d/m/Y - H:i:s');
        $user = AuthenticationService::get();
        return "$time [$severity] [$action] : [$user->email] : $message";
    }

    public static function notice($action, $message): string
    {
        return self::build(self::$severities['notice'], self::$actions[$action], $message);
    }

    public static function warning($action, $message): string
    {
        return self::build(self::$severities['warning'], self::$actions[$action], $message);
    }

    public static function danger($action, $message): string
    {
        return self::build(self::$severities['danger'], self::$actions[$action], $message);
    }

    public static function emergency($action, $message): string
    {
        return self::build(self::$severities['emergency'], self::$actions[$action], $message);
    }

    public static function updateOutput(Defender $defender, string $output, array $more = []): void
    {
        $newOutput = $defender->output ?? '';
        $newOutput .= "\n$output";
        $updated = ['output' => $newOutput];
        if (count($more) > 0)
        {
            $updated = array_merge($updated, $more);
        }
        $defender->update($updated);
    }
}
