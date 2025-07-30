<?php

namespace App\Tables\Actions;

use App\Services\FilamentTableService;
use Filament\Tables\Actions\DeleteBulkAction;

class FingerprintAction
{
    public static function actionGroup()
    {
        return FilamentTableService::actionGroup(
            view: true,
            edit: false,
            delete: true,
        );
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }
}
