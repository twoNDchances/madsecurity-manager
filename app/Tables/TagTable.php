<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Tables\Actions\TagAction;

class TagTable
{
    private static $action = TagAction::class;

    public static function name()
    {
        $description = fn($record) => $record->description;
        return FilamentTableService::text('name')
        ->description($description)
        ->wrap();
    }

    public static function color()
    {
        return FilamentTableService::color('color');
    }

    public static function types($name)
    {
        return FilamentTableService::text($name)
        ->bulleted()
        ->listWithLineBreaks()
        ->limitList(3)
        ->expandableLimitedList()
        ->wrap();
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
