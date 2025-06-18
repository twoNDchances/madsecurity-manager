<?php

namespace App\Tables;

use App\Services\AuthenticationService;
use App\Services\FilamentFormService;
use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class RuleTable
{
    public static function representation()
    {
        $description = fn($record) => $record->alias;
        return FilamentTableService::text('name', 'Name/Alias')
        ->description($description)
        ->wrap();
    }

    public static function phase()
    {
        $colors = fn($state) => match ($state)
        {
            0 => 'sky',
            1 => 'indigo',
            2 => 'primary',
            3 => 'rose',
            4 => 'danger',
            5 => 'pink',
        };
        return FilamentTableService::text('phase')
        ->badge()
        ->color($colors);
    }

    public static function target()
    {
        return FilamentTableService::text('getTarget.alias', 'Target Alias')
        ->wrap();
    }

    public static function inverse()
    {
        $user = AuthenticationService::get();
        $can = AuthenticationService::can($user, 'rule', 'update');
        if ($can) {
            return FilamentTableService::toggle('inverse');
        }
        return FilamentTableService::icon('inverse');
    }

    public static function comparator()
    {
        $format = fn($state) => Str::headline(str_replace('@', '', $state));
        return FilamentTableService::text('comparator')
        ->formatStateUsing($format);
    }

    public static function value()
    {
        $tooltip = function ($column)
        {
            $state = $column->getState();
            if (strlen($state) <= $column->getCharacterLimit()) {
                return null;
            }
            return $state;
        };
        return FilamentTableService::text('value')
        ->limit(8)
        ->tooltip($tooltip);
    }

    public static function wordlist()
    {
        return FilamentTableService::text('getWordlist.alias', 'Wordlist Alias');
    }

    public static function action()
    {
        $format = fn($state) => Str::headline($state);
        return FilamentTableService::text('action')
        ->formatStateUsing($format);
    }

    public static function severity()
    {
        $format = fn($state) => Str::upper($state);
        $colors = fn($state) => [
            'notice' => 'info',
            'warning' => 'warning',
            'error' => 'danger',
            'critical' => 'purple',
        ][$state];
        return FilamentTableService::text('severity')
        ->badge()
        ->color($colors)
        ->formatStateUsing($format);
    }

    public static function tags()
    {
        return TagFieldService::getTags();
    }

    public static function owner()
    {
        $colors = fn($state) => match ($state)
        {
            config('manager.account.email') => 'alternative',
            default => 'primary',
        };
        return FilamentTableService::text('getOwner.email', 'Created by')
        ->badge()
        ->color($colors);
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
