<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Decision;
use App\Models\Defender;
use App\Models\Fingerprint;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Policy;
use App\Models\Rule;
use App\Models\Tag;
use App\Models\Target;
use App\Models\Token;
use App\Models\User;
use App\Models\Wordlist;
use Filament\Widgets\ChartWidget;

class SystemDoughnutChart extends ChartWidget
{
    protected static ?string $heading = 'Popular Resource Doughnut Chart';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'data' => $this->getRecordCounts(),
                    'backgroundColor' => array_keys($this->getColorsForModels()),
                    'borderColor' => array_keys($this->getColorsForModels()),
                ],
            ],
            'labels' => array_values($this->getColorsForModels()),
        ];
    }

    private function getRecordCounts()
    {
        $models = [
            Decision::class, Defender::class, Group::class, Rule::class,
            Target::class, Wordlist::class, Asset::class, Tag::class,
            Permission::class, Policy::class, Token::class, User::class,
        ];
        $count = [];
        foreach ($models as $model)
        {
            $count[] = Fingerprint::whereMorphedTo('resource', $model)->count();
        }
        return $count;
    }

    private function getColorsForModels()
    {
        return [
            '#ef4444' => 'Decisons',
            '#a855f7' => 'Defenders',
            '#22c55e' => 'Groups',
            '#3b82f6' => 'Rules',
            '#eab308' => 'Targets',
            '#64748b' => 'Wordlists',
            '#0ea5e9' => 'Assets',
            '#f97316' => 'Tags',
            '#06b6d4' => 'Permissions',
            '#ec4899' => 'Policies',
            '#6366f1' => 'Tokens',
            '#14b8a6' => 'Users',
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
