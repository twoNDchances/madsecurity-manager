<?php

namespace App\Forms\Actions;

use App\Services\HttpRequestService;
use Filament\Forms\Components\Actions\Action;

class ReportAction
{
    public static function pingDefender()
    {
        $action = function($record)
        {
            $defender = $record->getDefender;
            if ($defender->protection)
            {
                HttpRequestService::perform(
                    $defender->health_method,
                    "$defender->url$defender->health",
                    null,
                    true,
                    $defender->username,
                    $defender->password,
                    $defender->certification ? storage_path("app/$defender->certification") : null,
                );
                return;
            }
            HttpRequestService::perform(
                $defender->health_method,
                "$defender->url$defender->health",
                null,
                true,
                null,
                null,
                $defender->certification ? storage_path("app/$defender->certification") : null,
            );
        };
        return Action::make('ping_defender')
        ->label('Ping Defender')
        ->action($action)
        ->icon('heroicon-o-arrow-top-right-on-square');
    }
}
