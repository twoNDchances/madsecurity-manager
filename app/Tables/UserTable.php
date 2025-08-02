<?php

namespace App\Tables;

use App\Services\IdentificationService;
use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Tables\Actions\UserAction;

class UserTable
{
    private static $action = UserAction::class;

    public static function name()
    {
        return FilamentTableService::text('name', null);
    }

    public static function email()
    {
        return FilamentTableService::text('email', null);
    }

    public static function activation()
    {
        $user = IdentificationService::get();
        if (IdentificationService::can($user, 'user', 'update'))
        {
            return FilamentTableService::toggle('active', 'Activated');
        }
        return FilamentTableService::icon('active', 'Activated');
    }

    public static function verification()
    {
        return FilamentTableService::icon('email_verified_at', 'Verified')->boolean();
    }

    public static function policies()
    {
        return FilamentTableService::text('policies.name', 'Policies')
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
        return FilamentTableService::text('getSuperior.email', 'Created by');
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
