<?php

namespace App\Validators;

class PolicyValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    public static function permissions()
    {
        return [
            'nullable',
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
            'exists:users,id',
        ];
    }
}
