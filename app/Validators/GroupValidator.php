<?php

namespace App\Validators;

use Illuminate\Validation\Rule;

class GroupValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'alpha_dash',
            function($record)
            {
                if ($record)
                {
                    return Rule::unique('groups', 'name')->ignore($record->id);
                }
                return 'unique:groups,name';
            },
        ];
    }

    public static function executionOrder()
    {
        return [
            'required',
            'integer',
            'min:1',
        ];
    }

    public static function level()
    {
        return [
            'required',
            'integer',
            'min:1',
        ];
    }

    public static function rules()
    {
        return [
            'nullable',
            'array',
            'exists:rules,id',
        ];
    }

    public static function defenders()
    {
        return [
            'nullable',
            'array',
            'exists:defenders,id',
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
