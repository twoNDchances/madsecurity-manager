<?php

namespace App\Validators;

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
            'required',
            'string',
        ];
    }
}
