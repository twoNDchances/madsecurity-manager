<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TimeService
{
    public static function getLastDates(?int $days)
    {
        $today = Carbon::today('Asia/Ho_Chi_Minh');
        $startDate = $today->copy()->subDays($days)->startOfDay();
        $endDate = $today->copy()->endOfDay();
        return CarbonPeriod::create($startDate, $endDate);
    }

    public static function getArrayLastFormatDates(?int $days, string $format = 'd/m')
    {
        $period = self::getLastDates($days);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format($format);
        }
        return $dates;
    }
}
