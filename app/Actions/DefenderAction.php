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

    private static function generalPostAction($record, $livewire)
    {
        $record = $record->toArray();
        unset($record['groups'], $record['tags']);
        $livewire->form->fill($record);
    }

    public static function checkHealth()
    {
        $action = function($record, $livewire)
        {
            DefenderHealthService::perform($record);
            self::generalPostAction($record, $livewire);
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
        $action = function($record, $livewire)
        {
            $record = DefenderSyncService::perform($record);
            self::generalPostAction($record, $livewire);
        };
        return Action::make('sync')
        ->icon('heroicon-o-arrow-down-on-square-stack')
        ->color('teal')
        ->action($action)
        ->requiresConfirmation()
        ->modalDescription('This action will sync data from Defender to Manager, are you sure you would like to do this?')
        ->authorize(self::can('sync'));
    }

    public static function apply()
    {
        $action = function($record, $livewire)
        {
            $record = DefenderApplyService::performAll($record);
            self::generalPostAction($record, $livewire);
        };
        return Action::make('apply_all')
        ->label('Apply')
        ->icon('heroicon-o-arrow-up-on-square-stack')
        ->color('sky')
        ->action($action)
        ->requiresConfirmation()
        ->modalHeading('Apply all')
        ->authorize(self::can('apply'));
    }

    public static function revoke()
    {
        $action = function($record, $livewire)
        {
            $record = DefenderRevokeService::performAll($record);
            self::generalPostAction($record, $livewire);
        };
        return Action::make('revoke_all')
        ->label('Revoke')
        ->icon('heroicon-o-arrow-uturn-left')
        ->color('pink')
        ->action($action)
        ->requiresConfirmation()
        ->modalHeading('Revoke all')
        ->authorize(self::can('revoke'));
    }
}
