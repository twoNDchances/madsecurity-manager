<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use Filament\Tables\Actions\DeleteBulkAction;

class TagTable
{
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
        return FilamentTableService::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }
}
