<?php

namespace App\Actions;

use Filament\Actions\Action;

class DefenderAction
{
    public static function checkHealth()
    {
        $action = function()
        {

        };
        return Action::make('check_health')
        ->icon('heroicon-o-question-mark-circle')
        ->label('Check')
        ->color('slate')
        ->action($action);
    }

    public static function sync()
    {
        $action = function()
        {

        };
        return Action::make('sync')
        ->icon('heroicon-o-arrow-down-on-square-stack')
        ->color('teal')
        ->action($action);
    }
}
