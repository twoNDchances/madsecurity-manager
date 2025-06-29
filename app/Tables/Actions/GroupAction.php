<?php

namespace App\Tables\Actions;

use App\Services\AuthenticationService;
use App\Services\DefenderApplyService;
use App\Services\DefenderRevokeService;
use App\Services\FilamentTableService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;

class GroupAction
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
                self::apply(),
                self::revoke(),
            ],
        );
    }

    private static function can($action)
    {
        $user = AuthenticationService::get();
        return AuthenticationService::can($user, 'defender', $action);
    }

    public static function apply()
    {
        $action = function ($record, $livewire)
        {
            DefenderApplyService::performEach($record, $livewire->getOwnerRecord());
            $livewire->dispatch('refreshDefenderForm');
        };
        return Action::make('apply')
        ->icon('heroicon-o-arrow-up-on-square-stack')
        ->color('sky')
        ->action($action)
        ->authorize(self::can('apply'));
    }

    public static function revoke()
    {
        $action = function ($record, $livewire)
        {
            DefenderRevokeService::performEach($record, $livewire->getOwnerRecord());
            $livewire->dispatch('refreshDefenderForm');
        };
        return Action::make('revoke')
        ->icon('heroicon-o-arrow-uturn-left')
        ->color('pink')
        ->requiresConfirmation()
        ->action($action)
        ->authorize(self::can('apply'));
    }
}
