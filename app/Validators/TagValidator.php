<?php

namespace App\Validators;

class TagValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    public static function color()
    {
        return [
            'required',
            'string',
            'size:7',
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }
}
