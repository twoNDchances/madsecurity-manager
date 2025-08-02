<?php

namespace App\Validators\GUI;

class DefenderValidator
{
    public static array $methods = [
        'post' => 'POST',
        'put' => 'PUT',
        'patch' => 'PATCH',
        'delete' => 'DELETE',
    ];

    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    public static function groups()
    {
        return [
            'nullable',
            'array',
            'exists:groups,id',
        ];
    }

    public static function url()
    {
        return [
            'required',
            'string',
            'url',
            function($record)
            {
                if ($record)
                {
                    return "unique:defenders,url,$record->id";
                }
                return 'unique:defenders,url';
            }
        ];
    }

    public static function path()
    {
        return [
            'required',
            'string',
            'starts_with:/'
        ];
    }

    public static function method()
    {
        return [
            'required',
            'string',
            'in:' . implode(',', array_keys(self::$methods)),
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function important()
    {
        return [
            'required',
            'boolean',
        ];
    }

    public static function periodic()
    {
        return [
            'required',
            'boolean',
        ];
    }

    public static function protection()
    {
        return [
            'required',
            'boolean',
        ];
    }

    public static function username()
    {
        return [
            'required_if:protection,true',
            'string',
            'max:255',
        ];
    }

    public static function password()
    {
        return [
            'required_if:protection,true',
            'string',
            'min:8',
            'max:255',
        ];
    }

    public static function decisions()
    {
        return [
            'nullable',
            'array',
            'exists:decisions,id',
        ];
    }
}
