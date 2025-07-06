<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SystemStat;
use App\Services\AuthenticationService;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return AuthenticationService::render([
            SystemStat::class,
        ]);
    }
}
