<?php

namespace App\Tables\Actions;

use App\Services\IdentificationService;
use App\Services\DefenderImplementService;
use App\Services\DefenderSuspendService;
use App\Services\FilamentTableService;
use App\Services\FingerprintService;
use App\Services\NotificationService;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;

class DecisionAction
{
    public static function refreshTable()
    {
        $action = function($livewire)
        {
            $livewire->dispatch('refreshDecisionTable');
            NotificationService::notify('success', 'Refreshed', 'Decision table has been refreshed');
        };
        return Action::make('refresh')
        ->label('Refresh Decision')
        ->action($action)
        ->color('warning')
        ->icon('heroicon-o-arrow-path');
    }

    public static function actionGroup()
    {
        return FilamentTableService::actionGroup();
    }

    private static function editDecision()
    {
        $url = fn($record) => route('filament.manager.resources.decisions.edit', $record->id);
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
            $actions[] = self::editDecision();
        }
        $actions = array_merge($actions, [
            self::implement(),
            self::suspend(),
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

    private static function implement()
    {
        $action = function ($record, $livewire)
        {
            DefenderImplementService::performEach($record, $livewire->getOwnerRecord());
            $livewire->dispatch('refreshDefenderForm');
            FingerprintService::generate($record, 'Implement');
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
            FingerprintService::generate($record, 'Suspend');
        };
        return Action::make('suspend')
        ->icon('heroicon-o-bolt-slash')
        ->color('yellow')
        ->requiresConfirmation()
        ->action($action)
        ->authorize(self::can('suspend'));
    }
}
