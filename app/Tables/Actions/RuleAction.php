<?php

namespace App\Tables\Actions;

use App\Services\FilamentTableService;
use App\Services\NotificationService;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class RuleAction
{
    public static function refreshTable()
    {
        $action = function($livewire)
        {
            $livewire->dispatch('refreshRuleTable');
            NotificationService::notify('info', 'Refreshed', 'Rule table has been refreshed');
        };
        return Action::make('refresh')
        ->label('Refresh Rule')
        ->action($action)
        ->icon('heroicon-o-arrow-path');
    }

    public static function actionGroup($custom = false)
    {
        if (!$custom)
        {
            return FilamentTableService::actionGroup();
        }
        return FilamentTableService::actionGroup(
            false,
            false,
            true,
            [
                self::viewRule(),
                self::editRule(),
            ],
        );
    }

    private static function viewRule()
    {
        return ViewAction::make()
        ->modalWidth(MaxWidth::SevenExtraLarge);
    }

    private static function editRule()
    {
        $url = fn($record) => route('filament.manager.resources.rules.edit', $record->id);
        return EditAction::make()
        ->url($url)
        ->openUrlInNewTab();
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }
}
