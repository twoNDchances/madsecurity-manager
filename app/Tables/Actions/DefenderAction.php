<?php

namespace App\Tables\Actions;

use App\Services\IdentificationService;
use App\Services\FilamentTableService;
use App\Services\NotificationService;
use Filament\Tables\Actions\DeleteBulkAction;

class DefenderAction
{
    public static function actionGroup()
    {
        return FilamentTableService::actionGroup();
    }

    public static function deleteBulkAction()
    {
        $action = function ($records)
        {
            $user = IdentificationService::get();
            $counter = 0;
            foreach ($records as $record)
            {
                if (!$user->important && $record->important) continue;
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
