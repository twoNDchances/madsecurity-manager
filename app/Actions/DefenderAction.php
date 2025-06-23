<?php

namespace App\Actions;

use App\Services\DefenderConsoleService;
use App\Services\HttpRequestService;
use Filament\Actions\Action;

class DefenderAction
{
    public static function checkHealth()
    {
        $action = function($livewire, $record)
        {
            $response = null;
            $lastStatus = false;
            if ($record->protection)
            {
                $response = HttpRequestService::perform(
                    'get',
                    "$record->url$record->health",
                    null,
                    true,
                    $record->username,
                    $record->password,
                );
            }
            else
            {
                $response = HttpRequestService::perform('get', "$record->url$record->health");
            }

            $output = null;
            if (is_string($response))
            {
                $output = DefenderConsoleService::warning($response);
            }
            else
            {
                $body = 'Status Code: ' . $response->status() . ' | Body: ' . $response->body();
                $output = DefenderConsoleService::notice($body);
                if ($response->successful())
                {
                    $lastStatus = true;
                }
            }
            $newOutput = $record->output;
            $newOutput[] = $output;
            $record->update([
                'lastStatus' => $lastStatus,
                'output' => $newOutput,
            ]);
            $livewire->form->fill($record->toArray());
        };
        return Action::make('check_health')
        ->icon('heroicon-o-question-mark-circle')
        ->label('Check')
        ->color('slate')
        ->action($action);
    }

    public static function sync()
    {
        $action = function()
        {

        };
        return Action::make('sync')
        ->icon('heroicon-o-arrow-down-on-square-stack')
        ->color('teal')
        ->action($action);
    }
}
