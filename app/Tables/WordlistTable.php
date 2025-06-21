<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Tables\Actions\WordlistAction;

class WordlistTable
{
    private static $action = WordlistAction::class;

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
        return self::$action::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return self::$action::deleteBulkAction();
    }
}
