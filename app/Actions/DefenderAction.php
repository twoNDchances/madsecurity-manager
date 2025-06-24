<?php

namespace App\Actions;

use App\Services\AuthenticationService;
use App\Services\DefenderApplyService;
use App\Services\DefenderHealthService;
use App\Services\DefenderRevokeService;
use App\Services\DefenderSyncService;
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
            $record = DefenderHealthService::perform($record);
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
            $record = DefenderSyncService::perform($record);
        };
        return Action::make('sync')
        ->icon('heroicon-o-arrow-down-on-square-stack')
        ->color('teal')
        ->action($action)
        ->authorize(self::can('sync'));
    }

    public static function apply()
    {
        $action = function($record)
        {
            $record = DefenderApplyService::performAll($record);
        };
        return Action::make('apply_all')
        ->label('Apply')
        ->icon('heroicon-o-arrow-up-on-square-stack')
        ->color('sky')
        ->action($action)
        ->authorize(self::can('apply'));
    }

    public static function revoke()
    {
        $action = function($record)
        {
            $record = DefenderRevokeService::performAll($record);
        };
        return Action::make('revoke_all')
        ->label('Revoke')
        ->icon('heroicon-o-arrow-uturn-left')
        ->color('pink')
        ->action($action)
        ->requiresConfirmation()
        ->modalHeading('Revoke All')
        ->authorize(self::can('revoke'));
    }
}
