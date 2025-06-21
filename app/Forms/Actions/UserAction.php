<?php

namespace App\Forms\Actions;

use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Str;

class UserAction
{
    public static function generatePassword()
    {
        $action = function($set)
        {
            $set('password', Str::random(12));
        };
        return Action::make('password_creation')
        ->icon('heroicon-o-arrow-path')
        ->action($action);
    }
}
