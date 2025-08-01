<?php

namespace App\Validators\GUI;

use Illuminate\Validation\Rule;

class PolicyValidator
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
                    return Rule::unique('policies', 'name')->ignore($record->id);
                }
                return 'unique:policies,name';
            },
        ];
    }

    public static function permissions()
    {
        return [
            'nullable',
            'array',
            'exists:permissions,id',
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function users()
    {
        return [
            'nullable',
            'array',
            'exists:users,id',
        ];
    }
}
