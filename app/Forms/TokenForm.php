<?php

namespace App\Forms;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Forms\Actions\TokenAction;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\GUI\TokenValidator;
use Filament\Resources\Pages\CreateRecord;

class TokenForm
{
    private static $validator = TokenValidator::class;

    private static $action = TokenAction::class;

    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'Token Name',
            self::$validator::name(),
        )
        ->required()
        ->unique(ignoreRecord: true);
    }

    public static function expiredAt()
    {
        return FilamentFormService::dateTimePicker(
            'expired_at',
            'Expired At',
            self::$validator::expiredAt(),
        );
    }

    public static function value()
    {
        $condition = fn($livewire) => $livewire instanceOf CreateRecord;
        $length = fn($livewire) => $condition($livewire) ? 48 : null;
        $helperText = 'Copy Token as soon as click generate, Token will be hashed after saving';
        return FilamentFormService::textInput(
            'value',
            null,
            'Value',
            self::$validator::value(),
        )
        ->required($condition)
        ->unique(ignoreRecord: true)
        ->minLength($length)
        ->maxLength(48) 
        ->readOnly()
        ->password()
        ->revealable()
        ->helperText($helperText)
        ->suffixAction(self::$action::generateToken());
    }

    public static function description()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Some Description about this Token',
        )
        ->rules(self::$validator::description());
    }

    public static function users($form = true)
    {
        $userField = FilamentFormService::select(
            'users',
            'Users',
            self::$validator::users(),
        )
        ->relationship('users', 'email')
        ->multiple()
        ->searchable()
        ->preload();
        if ($form)
        {
            $former = [
                UserResource::main(false, false, true),
            ];
            $creator = function(array $data)
            {
                $user = CreateUser::callByStatic($data);
                if (isset($data['tags']))
                {
                    $user->tags()->sync($data['tags']);
                }
                if (isset($data['policies']))
                {
                    $user->policies()->sync($data['policies']);
                }
                if (isset($data['tokens']))
                {
                    $user->tokens()->sync($data['tokens']);
                }
                return $user->id;
            };
            $userField = $userField
            ->createOptionForm($former)
            ->createOptionUsing($creator);
        }
        return $userField;
    }

    public static function tags($dehydrated = false)
    {
        return TagFieldService::setTags($dehydrated);
    }
}
