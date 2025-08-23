<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class SystemRadarChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            'labels' => ['Decisons', 'Defenders']
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }
}
