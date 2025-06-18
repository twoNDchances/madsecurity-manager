<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Services\NotificationService;
use App\Services\TagFieldService;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class TargetTable
{
    private static array $datatypeColors = [
        'warning' => 'array',
        'success' => 'number',
        'info' => 'string',
    ];

    public static function name()
    {
        $tooltip = function ($column)
        {
            $state = $column->getState();
            if (strlen($state) <= $column->getCharacterLimit()) {
                return null;
            }
            return $state;
        };
        return FilamentTableService::text('name')
        ->limit(16)
        ->tooltip($tooltip);
    }

    public static function phase()
    {
        return FilamentTableService::text('phase');
    }

    public static function alias()
    {
        $tooltip = function ($column)
        {
            $state = $column->getState();
            if (strlen($state) <= $column->getCharacterLimit()) {
                return null;
            }
            return $state;
        };
        return FilamentTableService::text('alias')
        ->limit(16)
        ->tooltip($tooltip);
    }

    public static function wordlist()
    {
        return FilamentTableService::text('getWordlist.alias', 'Wordlist')
        ->bulleted()
        ->limitList(3)
        ->expandableLimitedList()
        ->listWithLineBreaks();
    }

    public static function datatype()
    {
        return FilamentTableService::text('datatype')
        ->badge()
        ->colors(self::$datatypeColors);
    }

    public static function engine()
    {
        $format = fn($state) => Str::headline($state);
        return FilamentTableService::text('engine')
        ->formatStateUsing($format);
    }

    public static function finalDatatype()
    {
        return FilamentTableService::text(
            'final_datatype',
            'Final Datatype'
        )
        ->badge()
        ->colors(self::$datatypeColors);
    }

    public static function superior()
    {
        $tooltip = function ($column)
        {
            $state = $column->getState();
            if (strlen($state) <= $column->getCharacterLimit()) {
                return null;
            }
            return $state;
        };
        return FilamentTableService::text(
            'getSuperior.alias',
            'Referer'
        )
        ->badge()
        ->limit(16)
        ->tooltip($tooltip)
        ->color('cyan');
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
