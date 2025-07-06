<?php

namespace App\Services;

use Filament\Widgets\StatsOverviewWidget\Stat;

class FilamentWidgetService
{
    public static function stat($label, $value, $icon = null, $description = null)
    {
        return Stat::make($label, $value)
        ->icon($icon)
        ->description($description);
    }
}
