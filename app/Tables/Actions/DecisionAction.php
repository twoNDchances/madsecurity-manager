<?php

namespace App\Tables\Actions;

use App\Services\AuthenticationService;
use App\Services\DefenderImplementService;
use App\Services\DefenderSuspendService;
use App\Services\FilamentTableService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;

class DecisionAction
{
    public static function actionGroup()
    {
        return FilamentTableService::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }

    public static function operationActionGroup()
    {
        return FilamentTableService::actionGroup(
            true,
            true,
            true,
            [
                self::implement(),
                self::suspend(),
            ],
        );
    }

    private static function can($action)
    {
        $user = AuthenticationService::get();
        return AuthenticationService::can($user, 'defender', $action);
    }

    private static function implement()
    {
        $action = function ($record, $livewire)
        {
            DefenderImplementService::performEach($record, $livewire->getOwnerRecord());
            $livewire->dispatch('refreshDefenderForm');
        };
        return Action::make('implement')
        ->icon('heroicon-o-bolt')
        ->color('orange')
        ->action($action)
        ->authorize(self::can('implement'));
    }

    private static function suspend()
    {
        $action = function ($record, $livewire)
        {
            DefenderSuspendService::performEach($record, $livewire->getOwnerRecord());
            $livewire->dispatch('refreshDefenderForm');
        };
        return Action::make('suspend')
        ->icon('heroicon-o-bolt-slash')
        ->color('yellow')
        ->requiresConfirmation()
        ->action($action)
        ->authorize(self::can('suspend'));
    }
}
