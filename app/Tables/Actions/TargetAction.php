<?php

namespace App\Tables\Actions;

use App\Services\FilamentTableService;
use App\Services\NotificationService;
use Filament\Tables\Actions\DeleteBulkAction;

class TargetAction
{
    public static function actionGroup()
    {
        return FilamentTableService::actionGroup();
    }

    public static function deleteBulkAction()
    {
        $action = function ($records)
        {
            $counter = 0;
            foreach ($records as $record)
            {
                if ($record->immutable) continue;
                $record->delete();
                $counter++;
            }
            if ($counter == 0)
            {
                NotificationService::notify('failure', 'Fail','No records can be deleted');
                return;
            }
            NotificationService::notify('success','Deleted successfully', "Deleted $counter records");
        };
        return DeleteBulkAction::make()->action($action);
    }
}
