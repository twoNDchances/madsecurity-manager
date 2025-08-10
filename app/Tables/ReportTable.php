<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Tables\Actions\ReportAction;

class ReportTable
{
    private static $action = ReportAction::class;

    public static function time()
    {
        return FilamentTableService::text('time', 'At')
        ->dateTime('H:i:s - d/m/Y');
    }

    public static function defender()
    {
        $url = fn($record) => $record->getDefender->url . $record->getDefender->health;
        return FilamentTableService::text('getDefender.name', 'Defender')
        ->url($url)
        ->openUrlInNewTab();
    }

    public static function clientIp()
    {
        return FilamentTableService::text('client_ip', 'IP');
    }

    public static function path()
    {
        return FilamentTableService::text('path', 'Path');
    }

    public static function rule()
    {
        $description = fn($record) => $record->getRule->alias;
        return FilamentTableService::text('getRule.name', 'Rule')
        ->description($description);
    }

    public static function actionGroup()
    {
        return self::$action::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return self::$action::deleteBulkAction();
    }
}
