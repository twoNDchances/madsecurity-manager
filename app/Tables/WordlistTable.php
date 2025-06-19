<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use Filament\Tables\Actions\DeleteBulkAction;

class WordlistTable
{
    public static function name()
    {
        return FilamentTableService::text('name');
    }

    public static function alias()
    {
        return FilamentTableService::text('alias');
    }

    public static function counter()
    {
        return FilamentTableService::text('words_count')->counts('words');
    }

    public static function used($name, $label)
    {
        return FilamentTableService::text($name, $label)
        ->bulleted()
        ->limitList(3)
        ->expandableLimitedList()
        ->listWithLineBreaks();
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
        return FilamentTableService::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }
}
