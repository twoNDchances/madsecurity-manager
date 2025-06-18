<?php

namespace App\Tables;

use App\Services\AuthenticationService;
use App\Services\FilamentTableService;
use App\Services\NotificationService;
use App\Services\TagFieldService;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class UserTable
{
    public static function name()
    {
        return FilamentTableService::text('name', null);
    }

    public static function email()
    {
        return FilamentTableService::text('email', null);
    }

    public static function activation()
    {
        $user = AuthenticationService::get();
        if (AuthenticationService::can($user, 'user', 'update'))
        {
            return FilamentTableService::toggle('active', 'Activated');
        }
        return FilamentTableService::icon('active', 'Activated');
    }

    public static function verification()
    {
        return FilamentTableService::icon('email_verified_at', 'Verified')->boolean();
    }

    public static function policies()
    {
        return FilamentTableService::text('policies.name', 'Policies')
        ->listWithLineBreaks()
        ->bulleted()
        ->limitList(5)
        ->expandableLimitedList();
    }

    public static function tags()
    {
        return TagFieldService::getTags();
    }

    public static function owner()
    {
        return FilamentTableService::text('getSuperior.email', 'Created by');
    }

    public static function actionGroup()
    {
        return FilamentTableService::actionGroup(
            delete: false,
            more: [
                self::deleteAction(),
            ]
        );
    }

    public static function deleteAction()
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

    public static function deleteBulkAction()
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
}
