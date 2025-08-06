<?php

namespace App\Forms;

use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\GUI\WordlistValidator;

class WordlistForm
{
    private static $validator = WordlistValidator::class;

    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'Wordlist Name',
            self::$validator::name(),
        )
        ->required();
    }

    public static function alias()
    {
        return FilamentFormService::textInput(
            'alias',
            null,
            'Wordlist Alias',
            self::$validator::alias(),
        )
        ->alphaDash()
        ->required()
        ->unique(ignoreRecord: true);
    }

    public static function tags($dehydrated = false)
    {
        return TagFieldService::setTags($dehydrated);
    }

    public static function description()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Some Description about this Wordlist',
        )
        ->rules(self::$validator::description());
    }

    public static function content()
    {
        $state = function ($record, $set)
        {
            if ($record) {
                $set('content', $record->words()->pluck('content')->implode("\n"));
            }
        };
        return FilamentFormService::textarea(
            'content',
            'Content',
            'End a word with a new line'
        )
        ->afterStateHydrated($state)
        ->rules(self::$validator::content());
    }
}
