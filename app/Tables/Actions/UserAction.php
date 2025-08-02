<?php

namespace App\Tables\Actions;

use App\Services\IdentificationService;
use App\Services\FilamentTableService;
use App\Services\NotificationService;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class UserAction
{
    public static function actionGroup()
    {
        return FilamentTableService::actionGroup(
            delete: false,
            more: [
                self::deleteUser(),
            ]
        );
    }

    private static function deleteUser()
    {
        $action = function ($record)
        {
            $user = IdentificationService::get();
            if ($record->id == $user->id)
            {
                NotificationService::notify('failure', 'Delete self rejected');
                return;
            }
            $record->delete();
            NotificationService::notify('success', 'Deleted');
        };
        return DeleteAction::make()->action($action)
        ->requiresConfirmation();
    }

    public static function deleteBulkAction()
    {
        $action = function ($records)
        {
            $user = IdentificationService::get();
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
                NotificationService::notify('failure', 'Failed', 'No records can be deleted');
                return;
            }
            NotificationService::notify('success', 'Deleted', "Deleted $counter records");
        };
        return DeleteBulkAction::make()->action($action);
    }
}
