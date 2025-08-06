<?php

namespace App\Tables\Actions;

use App\Services\IdentificationService;
use App\Services\DefenderApplyService;
use App\Services\DefenderRevokeService;
use App\Services\FilamentTableService;
use App\Services\FingerprintService;
use App\Services\NotificationService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class GroupAction
{
    public static function refreshTable()
    {
        $action = function($livewire)
        {
            $livewire->dispatch('refreshGroupTable');
            NotificationService::notify('success', 'Refreshed', 'Group table has been refreshed');
        };
        return Action::make('refresh')
        ->label('Refresh Group')
        ->action($action)
        ->color('success')
        ->icon('heroicon-o-arrow-path');
    }

    public static function actionGroup()
    {
        return FilamentTableService::actionGroup();
    }

    private static function editGroup()
    {
        $url = fn($record) => route('filament.manager.resources.groups.edit', $record->id);
        return EditAction::make()
        ->url($url)
        ->openUrlInNewTab();
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }

    public static function operationActionGroup($custom = false)
    {
        $actions = [];
        if ($custom)
        {
            $actions[] = self::editGroup();
        }
        $actions = array_merge($actions, [
            self::apply(),
            self::revoke(),
        ]);
        return FilamentTableService::actionGroup(
            true,
            !$custom ? true : false,
            true,
            $actions,
        );
    }

    private static function can($action)
    {
        $user = IdentificationService::get();
        return IdentificationService::can($user, 'defender', $action);
    }

    public static function apply()
    {
        $action = function ($record, $livewire)
        {
            DefenderApplyService::performEach($record, $livewire->getOwnerRecord());
            $livewire->dispatch('refreshDefenderForm');
            FingerprintService::generate($record, 'Apply');
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
            FingerprintService::generate($record, 'Revoke');
        };
        return Action::make('revoke')
        ->icon('heroicon-o-arrow-uturn-left')
        ->color('pink')
        ->requiresConfirmation()
        ->action($action)
        ->authorize(self::can('apply'));
    }
}
