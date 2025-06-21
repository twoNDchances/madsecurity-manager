<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Tables\Actions\PolicyAction;

class PolicyTable
{
    private static $action = PolicyAction::class;

    public static function name()
    {
        return FilamentTableService::text('name');
    }

    public static function users()
    {
        return FilamentTableService::text('users.email', 'Users')
        ->listWithLineBreaks()
        ->bulleted()
        ->limitList(5)
        ->expandableLimitedList();
    }

    public static function permissions()
    {
        return FilamentTableService::text('permissions.name', 'Permissions')
        ->listWithLineBreaks()
        ->bulleted()
        ->limitList(5)
        ->expandableLimitedList();
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
