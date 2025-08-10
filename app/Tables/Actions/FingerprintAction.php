<?php

namespace App\Tables\Actions;

use App\Services\FilamentTableService;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction as ActionsViewAction;

class FingerprintAction
{
    public static function actionGroup()
    {
        return FilamentTableService::actionGroup(
            view: false,
            edit: false,
            delete: true,
            more: [
                self::viewFingerprint(),
            ],
        );
    }

    private static function viewFingerprint()
    {
        return ActionsViewAction::make()
        ->modalWidth(MaxWidth::SevenExtraLarge);
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }
}
