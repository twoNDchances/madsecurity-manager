<?php

namespace App\Actions;

use App\Services\AuthenticationService;
use App\Services\DefenderApplyService;
use App\Services\DefenderHealthService;
use App\Services\DefenderImplementService;
use App\Services\DefenderRevokeService;
use App\Services\DefenderSuspendService;
use App\Services\FingerprintService;
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
        $action = function($record, $livewire)
        {
            DefenderHealthService::perform($record);
            $livewire->dispatch('refreshDefenderForm');
            FingerprintService::generate($record, 'Check Health');
        };
        return Action::make('check_health')
        ->icon('heroicon-o-question-mark-circle')
        ->label('Check')
        ->color('slate')
        ->action($action)
        ->authorize(self::can('health'));
    }

    public static function collect()
    {
        $url = fn($record) => route('manager.collection') . '?id='. $record->id;
        return Action::make('collect')
        ->icon('heroicon-o-arrow-down-on-square-stack')
        ->color('teal')
        ->url($url)
        ->openUrlInNewTab()
        ->authorize(self::can('collect'));
    }

    public static function apply()
    {
        $action = function($record, $livewire)
        {
            $record = DefenderApplyService::performAll($record);
            $livewire->dispatch('refreshDefenderForm');
            $livewire->dispatch('refreshGroupTable');
            FingerprintService::generate($record, 'Apply All');
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
            $livewire->dispatch('refreshDefenderForm');
            $livewire->dispatch('refreshGroupTable');
            FingerprintService::generate($record, 'Revoke All');
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

    public static function implement()
    {
        $action = function($record, $livewire)
        {
            $record = DefenderImplementService::performAll($record);
            $livewire->dispatch('refreshDefenderForm');
            $livewire->dispatch('refreshDecisionTable');
            FingerprintService::generate($record, 'Implement All');
        };
        return Action::make('implement_all')
        ->label('Implement')
        ->icon('heroicon-o-bolt')
        ->color('orange')
        ->action($action)
        ->requiresConfirmation()
        ->modalHeading('Implement all')
        ->authorize(self::can('implement'));
    }

    public static function suspend()
    {
        $action = function($record, $livewire)
        {
            $record = DefenderSuspendService::performAll($record);
            $livewire->dispatch('refreshDefenderForm');
            $livewire->dispatch('refreshDecisionTable');
            FingerprintService::generate($record, 'Suspend All');
        };
        return Action::make('suspend_all')
        ->label('Suspend')
        ->icon('heroicon-o-bolt-slash')
        ->color('yellow')
        ->action($action)
        ->requiresConfirmation()
        ->modalHeading('Suspend all')
        ->authorize(self::can('suspend'));
    }
}
