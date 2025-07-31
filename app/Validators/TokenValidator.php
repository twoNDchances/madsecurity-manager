<?php

namespace App\Validators;

use Illuminate\Validation\Rule;

class TokenValidator
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
                    return Rule::unique('tokens', 'name')->ignore($record->id);
                }
                return 'unique:tokens,name';
            },
        ];
    }

    public static function key()
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    public static function value()
    {
        return [
            'required',
            'string',
            'min:48',
            'max:48',
            function($record)
            {
                if ($record)
                {
                    return Rule::unique('tokens', 'value')->ignore($record->id);
                }
                return 'unique:tokens,value';
            },
        ];
    }

    public static function locatedAt()
    {
        return [
            'required',
            'in:query,header',
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function expiredAt()
    {
        return [
            'nullable',
            'date',
        ];
    }
}
