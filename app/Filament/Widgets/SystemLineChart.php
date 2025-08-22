<?php

namespace App\Filament\Widgets;

use App\Models\Fingerprint;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class SystemLineChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            'datasets' => $this->getUserWithRecordCountsLastWeek(),
            'labels' => $this->getArrayLastWeekFormatDates(),
        ];
    }

    private function getLastWeekDates()
    {
        $today = Carbon::today('Asia/Ho_Chi_Minh');
        $startDate = $today->copy()->subWeek()->startOfDay();
        $endDate = $today->copy()->endOfDay();
        return CarbonPeriod::create($startDate, $endDate);
    }

    private function getArrayLastWeekFormatDates()
    {
        $period = $this->getLastWeekDates();
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('d/m');
        }
        return $dates;
    }

    private function getUserWithRecordCountsLastWeek()
    {
        $data = [];
        $users = User::all();
        foreach ($users as $user)
        {
            $data[] = [
                'label' => $user->email,
                'data' => $this->getRecordCountsLastWeek($user->id),
            ];
        }
        return $data;
    }

    private function getRecordCountsLastWeek($userId)
    {
        $period = $this->getLastWeekDates();
        $data = [];

        foreach ($period as $date) {
            $count = Fingerprint::query()->where('user_id', $userId)->whereDate(
                'created_at',
                $date->format('Y-m-d'),
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
