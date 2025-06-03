<?php

namespace App\Services;

use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class FilamentColumnService
{
    public static function actionGroup($view = true, $edit = true, $delete = true)
    {
        $actionGroup = [];
        if ($view)
        {
            $actionGroup[] = ViewAction::make();
        }
        if ($edit)
        {
            $actionGroup[] = EditAction::make();
        }
        if ($delete)
        {
            $actionGroup[] = DeleteAction::make();
        }
        return ActionGroup::make($actionGroup);
    }
}
