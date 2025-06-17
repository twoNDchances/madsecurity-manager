<?php

namespace App\Services;

use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class FilamentTableService
{
    public static function actionGroup($view = true, $edit = true, $delete = true, $more = [])
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
        if (count($more) > 0)
        {
            foreach ($more as $action)
            {
                $actionGroup[] = $action;
            }
        }
        return ActionGroup::make($actionGroup);
    }

    public static function deleteUserAction()
    {
        $action = function ($record)
        {
            $user = AuthenticationService::get();
            if ($record->id == $user->id)
            {
                NotificationService::notify('failure', 'Delete self rejected');
                return;
            }
            $record->delete();
            NotificationService::notify('success', 'Deleted');
        };
        return DeleteAction::make()->action($action);
    }

    public static function deleteUserBulkAction()
    {
        $action = function ($records)
        {
            $user = AuthenticationService::get();
            $counter = 0 ;
            foreach ($records as $record)
            {
                if ($record->important && !$user->important) continue;
                if ($record->id == $user->id) continue;
                $record->delete();
                $counter++;
            }
            if ($counter == 0)
            {
                NotificationService::notify('failure', 'Fail', 'No records can be deleted');
                return;
            }
            NotificationService::notify('success', 'Deleted successfully', "Deleted $counter records");
        };
        return DeleteBulkAction::make()->action($action);
    }

    public static function text($name, $label = null)
    {
        return TextColumn::make($name)->label($label)->searchable()->sortable()->toggleable();
    }

    public static function icon($name, $label = null)
    {
        return IconColumn::make($name)->label($label)->searchable()->sortable()->toggleable();
    }

    public static function toggle($name, $label = null)
    {
        return ToggleColumn::make($name)->label($label)->searchable()->sortable()->toggleable();
    }

    public static function color($name, $label = null)
    {
        return ColorColumn::make($name)->label($label)->searchable()->sortable()->toggleable()
        ->copyable()
        ->copyMessage('Color code copied')
        ->copyMessageDuration(1500);
    }
}
