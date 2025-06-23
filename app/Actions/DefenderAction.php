<?php

namespace App\Actions;

use App\Services\AuthenticationService;
use App\Services\DefenderActionService;
use Filament\Actions\Action;

class DefenderAction
{
    private static function can($action)
    {
        $user = AuthenticationService::get();
        return AuthenticationService::can($user, 'defender', $action);
    }

    public static function checkHealth()
    {
        $action = function($livewire, $record)
        {
            $record = DefenderActionService::health($record);
            $livewire->form->fill($record->toArray());
        };
        return Action::make('check_health')
        ->icon('heroicon-o-question-mark-circle')
        ->label('Check')
        ->color('slate')
        ->action($action)
        ->authorize(self::can('health'));
    }

    public static function sync()
    {
        $action = function($record)
        {
            $record = DefenderActionService::sync($record);
        };
        return Action::make('sync')
        ->icon('heroicon-o-arrow-down-on-square-stack')
        ->color('teal')
        ->action($action);
    }
}
