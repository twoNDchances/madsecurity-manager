<?php

namespace App\Forms\Actions;

use Filament\Forms\Components\Actions\Action;

class GroupAction
{
    public static function generateName()
    {
        $action = function($set)
        {
            $set('name', 'group-' . now()->timestamp);
        };
        return Action::make('generate_name')
        ->icon('heroicon-o-arrow-path')
        ->action($action);
    }

    public static function createRule()
    {
        $url = route('filament.manager.resources.rules.create');
        return Action::make('create_rule')
        ->label('Create Rule')
        ->icon('heroicon-o-plus')
        ->url($url)
        ->openUrlInNewTab()
        ->color('primary');
    }
}
