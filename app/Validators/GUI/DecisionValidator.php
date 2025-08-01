<?php

namespace App\Validators\GUI;

use Illuminate\Validation\Rule;

class DecisionValidator
{
    public static $phaseTypes = [
        'request' => 'Request',
        'response' => 'Response',
    ];

    public static $actions = [
        'request' => [
            'deny' => 'Deny',
            'redirect' => 'Redirect',
            'kill' => 'Kill',
            'tag' => 'Tag',
        ],
        'response' => [
            'deny' => 'Deny',
            'warn' => 'Warn',
        ],
    ];

    public static function score()
    {
        return [
            'required',
            'integer',
            'min:-999999999',
            'max:999999999',
        ];
    }

    public static function phaseType()
    {
        return [
            'required',
            'string',
            'in:' . implode(',', array_keys(self::$phaseTypes)),
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

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
                    return Rule::unique('decisions', 'name')->ignore($record->id);
                }
                return 'unique:decisions,name';
            },
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

    public static function action()
    {
        return [
            'required',
            'string',
            fn($get) => 'in:' . implode(',', array_keys(self::$actions[$get('phase_type')])),
        ];
    }

    public static function redirect()
    {
        return [
            'required_if:action,redirect',
            'string',
            'url',
        ];
    }

    public static function killHeader()
    {
        return [
            'required_if:action,kill',
            'string',
            'max:255',
        ];
    }

    public static function killPath()
    {
        return [
            'required_if:action,kill',
            'string',
            'starts_with:/'
        ];
    }

    public static function wordlist()
    {
        return [
            'required_if:action,tag,warn',
            'integer',
            'exists:wordlists,id',
        ];
    }
}
