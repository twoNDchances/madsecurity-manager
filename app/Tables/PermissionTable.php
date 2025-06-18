<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class PermissionTable
{
    public static function name()
    {
        return FilamentTableService::text('name');
    }

    public static function resource()
    {
        $state = fn($record) => Str::title(explode('.', $record->action)[0]);
        return FilamentTableService::text('resource')->getStateUsing($state);
    }

    public static function action()
    {
        $state = fn($record) => Str::headline(explode('.', $record->action)[1]);
        return FilamentTableService::text('action')->getStateUsing($state);
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
        return FilamentTableService::text('getOwner.email', 'Created by');
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
