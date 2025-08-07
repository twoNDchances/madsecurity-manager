<?php

namespace App\Validators\GUI;

class TagValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
            function($record)
            {
                if ($record)
                {
                    return "unique:tags,name,$record->id";
                }
                return 'unique:tags,name';
            },
        ];
    }

    public static function color()
    {
        return [
            'required',
            'string',
            'size:7',
            'starts_with:#'
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
