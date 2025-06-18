<?php

namespace App\Forms;

use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\UserValidator;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class UserForm
{
    private static $validator = UserValidator::class;
    
    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'User Name',
            self::$validator::name(),
        )
        ->required();
    }

    public static function email()
    {
        return FilamentFormService::textInput(
            'email',
            null,
            'User Email',
            self::$validator::email(),
        )
        ->required()
        ->unique(ignoreRecord: true)
        ->email();
    }

    public static function password()
    {
        $condition = fn($livewire) => $livewire instanceOf CreateRecord;
        $length = fn($livewire) => $condition($livewire) ? 4 : null;
        return FilamentFormService::textInput(
            'password',
            null,
            'User Password',
            self::$validator::password(),
        )
        ->required($condition)
        ->minLength($length)
        ->password()
        ->revealable()
        ->suffixAction(self::generatePassword());
    }

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

    public static function tags()
    {
        return TagFieldService::setTags();
    }

    public static function verification()
    {
        $condition = fn($livewire) => $livewire instanceOf CreateRecord;
        return FilamentFormService::toggle(
            'force_verification',
            'Force account verification',
            self::$validator::verification(),
        )
        ->helperText('This account will receive an email for verification before use.')
        ->visible($condition);
    }

    public static function policies()
    {
        return FilamentFormService::select(
            'policies',
            'Policies',
            self::$validator::policies(),
        )
        ->relationship('policies', 'name')
        ->multiple()
        ->searchable()
        ->preload();
    }

    public static function activation()
    {
        return FilamentFormService::toggle(
            'active',
            'Active account',
            self::$validator::activation(),
        )
        ->helperText('Turn on to start using this account.')
        ->default(true);
    }

    public static function important()
    {
        return FilamentFormService::toggle(
            'important',
            'Important account',
            self::$validator::important(),
        )
        ->helperText('This account will be on par with the root account.');
    }
}
