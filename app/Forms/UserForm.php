<?php

namespace App\Forms;

use App\Filament\Resources\PolicyResource;
use App\Filament\Resources\TokenResource;
use App\Forms\Actions\UserAction;
use App\Services\IdentificationService;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\GUI\UserValidator;
use Filament\Resources\Pages\CreateRecord;

class UserForm
{
    private static $validator = UserValidator::class;

    private static $action = UserAction::class;
    
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
        ->suffixAction(self::$action::generatePassword());
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

    public static function policies($form = true)
    {
        $policyField = FilamentFormService::select(
            'policies',
            'Policies',
            self::$validator::policies(),
        )
        ->relationship('policies', 'name')
        ->multiple()
        ->searchable()
        ->preload();
        if ($form)
        {
            $former = [
                PolicyResource::main(false, false, true),
            ];
            $policyField = $policyField
            ->createOptionForm($former);
        }
        return $policyField;
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
        $condition = fn() => !IdentificationService::get()->important;
        $helperText = function() use ($condition)
        {
            if ($condition())
            {
                return 'This feature can not use now.';
            }
            return 'This account will be on par with the root account.';
        };
        return FilamentFormService::toggle(
            'important',
            'Important account',
            self::$validator::important(),
        )
        ->helperText($helperText)
        ->disabled($condition);
    }

    public static function tokens($form = true)
    {
        $tokenField = FilamentFormService::select(
            'tokens',
            'Tokens',
            self::$validator::tokens(),
        )
        ->relationship('tokens', 'name')
        ->multiple()
        ->searchable()
        ->preload();
        if ($form)
        {
            $former = [
                TokenResource::main(false, true),
            ];
            $tokenField = $tokenField
            ->createOptionForm($former);
        }
        return $tokenField;
    }

    public static function owner()
    {
        return FilamentFormService::owner();
    }
}
