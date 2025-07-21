<?php

namespace App\Forms;

use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\DecisionValidator;

class DecisionForm
{
    private static $validator = DecisionValidator::class;

    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'Decision Name',
            self::$validator::name(),
        )
        ->required()
        ->unique(ignoreRecord: true);
    }

    public static function phaseType()
    {
        $colors = fn($state) => match ($state) {
            'request' => 'info',
            'response' => 'danger',
        };
        return FilamentFormService::toggleButton(
            'phase_type',
            'Phase Type',
            self::$validator::phaseType(),
            self::$validator::$phaseTypes,
        )
        ->required()
        ->colors($colors);
    }

    public static function score()
    {
        return FilamentFormService::textInput(
            'score',
            null,
            'Score',
            self::$validator::score(),
        )
        ->required()
        ->integer()
        ->maxValue(999999999)
        ->minValue(-999999999);
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
