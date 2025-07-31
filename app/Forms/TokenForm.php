<?php

namespace App\Forms;

use App\Forms\Actions\TokenAction;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\TokenValidator;

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

    public static function key()
    {
        return FilamentFormService::textInput(
            'key',
            null,
            'Key',
            self::$validator::key(),
        )
        ->required();
    }

    public static function value()
    {
        return FilamentFormService::textInput(
            'value',
            null,
            'Value',
            self::$validator::value(),
        )
        ->required()
        ->unique(ignoreRecord: true)
        ->minLength(48)
        ->maxLength(48)
        ->readOnly()
        ->suffixAction(self::$action::generateToken());
    }

    public static function tags()
    {
        return TagFieldService::setTags();
    }

    public static function owner()
    {
        return FilamentFormService::owner();
    }
}
