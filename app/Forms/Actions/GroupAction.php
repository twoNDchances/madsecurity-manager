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
}
