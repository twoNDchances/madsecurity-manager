<?php

namespace App\Forms\Actions;

use App\Models\Token;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class TokenAction
{
    public static function generateToken()
    {
        $action = function($set)
        {
            $value = null;
            while (true)
            {
                $value = Str::random(48);
                $token = Token::where('value', $value)->first();
                if (!$token)
                {
                    break;
                }
            }
            $set('value', $value);
        };
        $condition = fn($livewire) => !($livewire instanceof CreateRecord || $livewire instanceof EditRecord);
        return Action::make('generate_token')
        ->icon('heroicon-o-arrow-path')
        ->action($action)
        ->disabled($condition);
    }
}
