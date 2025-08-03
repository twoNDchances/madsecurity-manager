<?php

namespace App\Forms\Actions;

use App\Models\Token;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
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
                $alreadyExists = false;
                Token::cursor()->each(function ($token) use (&$alreadyExists, $value)
                {
                    if (Hash::check($value, $token->value))
                    {
                        $alreadyExists = true;
                        return false;
                    }
                });
                if (!$alreadyExists)
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
