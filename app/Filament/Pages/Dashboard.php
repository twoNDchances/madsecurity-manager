<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SystemBarChart;
use App\Filament\Widgets\SystemDoughnutChart;
use App\Filament\Widgets\SystemLineChart;
use App\Filament\Widgets\SystemRadarChart;
use App\Filament\Widgets\SystemStat;
use App\Services\IdentificationService;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected ?string $subheading = 'Manager - v1.0.0';

    protected function getHeaderWidgets(): array
    {
        return IdentificationService::render([
            SystemStat::class,
            SystemBarChart::class,
            SystemLineChart::class,
            SystemDoughnutChart::class,
            SystemRadarChart::class,
        ]);
    }
}
