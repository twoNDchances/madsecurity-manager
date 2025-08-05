<?php

namespace App\Forms\Actions;

use App\Services\HttpRequestService;
use App\Services\NotificationService;
use Filament\Forms\Components\Actions\Action;

class RuleAction
{
    public static function checkConnection()
    {
        $action = function($state, $get)
        {
            if (!$state)
            {
                NotificationService::notify('info', 'Info', 'Please enter a valid URL');
                return;
            }
            $body = [
                'errors' => [],
                'data' => ['message' => 'connected']
            ];
            HttpRequestService::perform($get('request_method'), $state, $body);
        };
        return Action::make('check_connection')
        ->icon('heroicon-o-check')
        ->action($action);
    }

    public static function createTarget()
    {
        $url = route('filament.manager.resources.targets.create');
        return Action::make('create_target')
        ->icon('heroicon-o-plus')
        ->url($url)
        ->openUrlInNewTab()
        ->color('primary')
        ->label('Create Target');
    }
}
