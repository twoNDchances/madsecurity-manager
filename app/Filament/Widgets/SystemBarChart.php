<?php

namespace App\Filament\Widgets;

use App\Models\Defender;
use App\Models\Report;
use App\Services\TimeService;
use Filament\Widgets\ChartWidget;

class SystemBarChart extends ChartWidget
{
    protected static ?string $heading = 'Defender Report Bar Chart';

    public ?string $filter = '3';

    protected function getData(): array
    {
        return [
            'datasets' => $this->getDefenderWithRecordCounts(),
            'labels' => TimeService::getArrayLastFormatDates($this->filter),
        ];
    }

    protected function getFilters(): array|null
    {
        return [
            0 => 'Today',
            3 => 'Last 3 days',
            7 => 'Last week',
            14 => 'Last 2 weeks',
        ];
    }

    private function getDefenderWithRecordCounts()
    {
        $data = [];
        $defenders = Defender::all();
        foreach ($defenders as $defender)
        {
            $build = [
                'label' => $defender->name,
                'data' => $this->getRecordCounts($defender->id),
            ];
            $tag = $defender->tags()->first();
            if ($tag)
            {
                $build['backgroundColor'] = $tag->color;
                $build['borderColor'] = $tag->color;
            }
            $data[] = $build;
        }
        return $data;
    }

    private function getRecordCounts($defenderId)
    {
        $period = TimeService::getLastDates($this->filter);
        $data = [];
        foreach ($period as $date) {
            $count = Report::query()->where('defender_id', $defenderId)->whereDate(
                'time',
                $date->timezone(config('app.timezone', 'Asia/Ho_Chi_Minh')),
            )->count();
            $data[] = $count;
        }
        return $data;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
