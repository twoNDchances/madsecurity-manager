<?php

namespace App\Forms;

use App\Services\FilamentFormService;
use App\Validators\TagValidator;

class TagForm
{
    private static $validator = TagValidator::class;

    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'Tag Name',
            self::$validator::name(),
        )
        ->required()
        ->unique(ignoreRecord: true);
    }

    public static function color()
    {
        return FilamentFormService::colorPicker(
            'color',
            null,
        )
        ->required()
        ->rules(self::$validator::color());
    }

    public static function description()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Some description for this Tag'
        )
        ->rules(self::$validator::description());
    }
}
