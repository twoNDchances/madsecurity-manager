<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Tables\Actions\AssetAction;

class AssetTable
{
    private static $action = AssetAction::class;

    public static function name()
    {
        return FilamentTableService::text('name');
    }

    public static function totalAsset()
    {
        return FilamentTableService::text('total_asset', 'Total Asset')
        ->badge()
        ->color('primary')
        ->numeric();
    }

    public static function totalResource()
    {
        return FilamentTableService::text('total_resource', 'Total Resource')
        ->badge()
        ->color('purple')
        ->numeric();
    }

    public static function failResource()
    {
        $color = fn($state) => $state == 0 ? 'success' : 'danger';
        return FilamentTableService::text('fail_resource', 'Fail Resource')
        ->badge()
        ->color($color)
        ->numeric();
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
