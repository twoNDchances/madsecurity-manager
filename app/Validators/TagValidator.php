<?php

namespace App\Validators;

use Illuminate\Validation\Rule;

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
                    return Rule::unique('tags', 'name')->ignore($record->id);
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
