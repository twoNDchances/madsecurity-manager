<?php

namespace App\Services;

use Carbon\Carbon;

class DefenderConsoleService
{
    private static array $severity = [
        'notice'   => 'NOTICE',
        'warning'  => 'WARNING',
        'danger'   => 'DANGER',
        'critical' => 'CRITICAL',
    ];

    private static function build($severity, $message): string
    {
        $time = Carbon::now()->format('d/m/Y - H:i:s');
        return $time . ' ' . str_pad("[$severity]", 10) . " : $message";
    }

    public static function notice(string $message): string
    {
        return self::build(self::$severity['notice'], $message);
    }

    public static function warning(string $message): string
    {
        return self::build(self::$severity['warning'], $message);
    }

    public static function danger(string $message): string
    {
        return self::build(self::$severity['danger'], $message);
    }

    public static function critical(string $message): string
    {
        return self::build(self::$severity['critical'], $message);
    }
}
