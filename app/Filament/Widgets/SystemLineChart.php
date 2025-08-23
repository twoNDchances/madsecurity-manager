<?php

namespace App\Filament\Widgets;

use App\Models\Fingerprint;
use App\Models\User;
use App\Services\TimeService;
use Filament\Widgets\ChartWidget;

class SystemLineChart extends ChartWidget
{
    protected static ?string $heading = 'User Activity Line Chart';

    public ?string $filter = '3';

    protected function getData(): array
    {
        return [
            'datasets' => $this->getUserWithRecordCounts(),
            'labels' => TimeService::getArrayLastFormatDates($this->filter),
        ];
    }

    protected function getFilters(): array|null
    {
        return [
            3 => 'Last 3 days',
            7 => 'Last week',
            14 => 'Last 2 weeks',
        ];
    }

    private function getUserWithRecordCounts()
    {
        $data = [];
        $users = User::all();
        foreach ($users as $user)
        {
            $build = [
                'label' => $user->email,
                'data' => $this->getRecordCounts($user->id),
            ];
            $tag = $user->tags()->first();
            if ($tag)
            {
                $build['backgroundColor'] = $tag->color;
                $build['borderColor'] = $tag->color;
            }
            $data[] = $build;
        }
        return $data;
    }

    private function getRecordCounts($userId)
    {
        $period = TimeService::getLastDates($this->filter);
        $data = [];
        foreach ($period as $date) {
            $count = Fingerprint::query()->where('user_id', $userId)->whereDate(
                'created_at',
                $date->timezone(config('app.timezone', 'Asia/Ho_Chi_Minh')),
            )->count();
            $data[] = $count;
        }
        return $data;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
