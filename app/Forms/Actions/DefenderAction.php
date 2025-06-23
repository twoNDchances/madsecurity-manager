<?php

namespace App\Forms\Actions;

use App\Services\HttpRequestService;
use App\Services\NotificationService;
use Filament\Forms\Components\Actions\Action;

class DefenderAction
{
    public static function checkHealth()
    {
        $action = function($state, $get)
        {
            if (!$state)
            {
                NotificationService::notify('info', 'Info', 'Please enter a valid URL');
                return;
            }
            if ($get('protection'))
            {
                HttpRequestService::perform(
                    'get',
                    $state . $get('health'),
                    null,
                    true,
                    $get('username'),
                    $get('password'),
                );
                return;
            }
            HttpRequestService::perform('get', $state . $get('health'));
        };
        return Action::make('check_health')
        ->icon('heroicon-o-check')
        ->action($action);
    }

    public static function clearOutput()
    {
        $action = function($record, $set)
        {
            if ($record)
            {
                $record->update(['output' => null]);
            }
            $set('output', null);
            NotificationService::notify('success', 'Cleared');
        };
        return Action::make('clear_output')
        ->label('Clear')
        ->action($action)
        ->icon('heroicon-o-backspace');
    }
}
