<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use Filament\Tables\Actions\DeleteBulkAction;

class RuleTable
{
    public static function tags()
    {
        return TagFieldService::getTags();
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
