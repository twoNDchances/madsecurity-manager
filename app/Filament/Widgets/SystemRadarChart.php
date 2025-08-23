<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use App\Models\Decision;
use App\Models\Defender;
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

class SystemRadarChart extends ChartWidget
{
    protected static ?string $heading = 'Usage Level Radar Chart';

    protected function getData(): array
    {
        return [
            'datasets' => $this->getUserWithRecordCounts(),
            'labels' => [
                'Decisons', 'Defenders', 'Groups', 'Rules',
                'Targets', 'Wordlists', 'Assets', 'Tags',
                'Permissions', 'Policies', 'Tokens', 'Users',
            ],
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

    private static function getRecordCounts($userId)
    {
        $models = [
            Decision::class, Defender::class, Group::class, Rule::class,
            Target::class, Wordlist::class, Asset::class, Tag::class,
            Permission::class, Policy::class, Token::class, User::class,
        ];
        $counts = [];
        foreach ($models as $model)
        {
            $counts[] = User::find($userId)->getFingerprints()->whereMorphedTo(
                'resource',
                $model,
            )->count();
        }
        return $counts;
    }

    protected function getType(): string
    {
        return 'radar';
    }
}
