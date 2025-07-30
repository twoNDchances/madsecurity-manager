<?php

namespace App\Forms\Actions;

use Filament\Forms\Components\Actions\Action;

class FingerprintAction
{
    public static function openResource()
    {
        $url = fn($state) => $state;
        return Action::make('open_resource')
        ->icon('heroicon-o-arrow-top-right-on-square')
        ->url($url)
        ->openUrlInNewTab();
    }
}
