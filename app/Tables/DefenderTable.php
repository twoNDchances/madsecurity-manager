<?php

namespace App\Tables;

use App\Services\AuthenticationService;
use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Tables\Actions\DefenderAction;

class DefenderTable
{
    private static $action = DefenderAction::class;

    public static function representation()
    {
        $url = fn($state, $record) => "$state$record->health";
        $description = fn($record) => $record->name;
        return FilamentTableService::text(
            'url',
            'Representation',
        )
        ->url($url)
        ->openUrlInNewTab()
        ->description($description, 'above');
    }

    public static function periodic()
    {
        $user = AuthenticationService::get();
        if (AuthenticationService::can($user, 'defender', 'update'))
        {
            return FilamentTableService::toggle('periodic');
        }
        return FilamentTableService::icon('periodic');
    }

    public static function lastStatus()
    {
        return FilamentTableService::icon('last_status', 'Last Status')
        ->boolean();
    }

    public static function groups()
    {
        return FilamentTableService::text('groups.name','Groups')
        ->listWithLineBreaks()
        ->bulleted()
        ->limitList(3)
        ->expandableLimitedList();
    }

    public static function health()
    {
        return FilamentTableService::text('health');
    }

    public static function apply()
    {
        return FilamentTableService::text('apply');
    }

    public static function revoke()
    {
        return FilamentTableService::text('revoke');
    }

    public static function implement()
    {
        return FilamentTableService::text('implement');
    }

    public static function suspend()
    {
        return FilamentTableService::text('suspend');
    }

    public static function protection()
    {
        return FilamentTableService::icon('protection')
        ->boolean();
    }

    public static function tags()
    {
        return TagFieldService::getTags();
    }

    public static function owner()
    {
        return FilamentTableService::text('getOwner.email', 'Created by');
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
