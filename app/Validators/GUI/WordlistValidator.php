<?php

namespace App\Validators\GUI;

class WordlistValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    public static function alias()
    {
        return [
            'required',
            'string',
            'max:255',
            'alpha_dash',
            function($record)
            {
                if ($record)
                {
                    return "unique:wordlists,alias,$record->id";
                }
                return 'unique:wordlists,alias';
            },
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function content()
    {
        return [
            'nullable',
            'string',
        ];
    }
}
