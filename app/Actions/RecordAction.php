<?php

namespace App\Actions;

use App\Services\NotificationService;
use Filament\Actions\Action;

class RecordAction
{
    public static function refresh()
    {
        $action = function($livewire)
        {
            $livewire->dispatch('refreshRecordTable');
            NotificationService::notify('success', 'Refreshed');
        };
        return Action::make('refresh')
        ->icon('heroicon-o-arrow-path')
        ->label('Refresh')
        ->action($action);
    }
}
