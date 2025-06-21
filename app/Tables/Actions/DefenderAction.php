<?php

namespace App\Tables\Actions;

use App\Services\FilamentTableService;
use Filament\Tables\Actions\DeleteBulkAction;

class DefenderAction
{
    public static function actionGroup()
    {
        return FilamentTableService::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }
}
