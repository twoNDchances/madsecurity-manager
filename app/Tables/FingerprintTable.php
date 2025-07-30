<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Tables\Actions\FingerprintAction;

class FingerprintTable
{
    private static $action = FingerprintAction::class;

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
