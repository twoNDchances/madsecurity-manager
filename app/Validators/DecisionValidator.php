<?php

namespace App\Validators;

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
            'suspect' => 'Suspect',
            'redirect' => 'Redirect',
            'tag' => 'Tag',
        ],
        'response' => [
            'deny' => 'Deny',
            'suspect' => 'Suspect',
            'warn' => 'Warn',
            'bait' => 'Bait',
        ],
    ];

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

    public static function phaseType()
    {
        return [
            'required',
            'string',
            'in:' . implode(',', array_keys(self::$phaseTypes)),
        ];
    }

    public static function score()
    {
        return [
            'required',
            'integer',
            'min:-999999999',
            'max:999999999',
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

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function redirect()
    {
        return [
            'required_if:action,redirect',
            'string',
        ];
    }

    public static function wordlist()
    {
        return [
            'required_if:action,tag,warn,bait',
            'integer',
            'exists:wordlists,id',
        ];
    }
}
