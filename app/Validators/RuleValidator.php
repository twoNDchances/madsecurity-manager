<?php

namespace App\Validators;

use App\Models\Target;

class RuleValidator
{
    public static array $phases = [
        0 => '0. Full Request',
        1 => '1. Request Header',
        2 => '2. Request Body',
        3 => '3. Response Header',
        4 => '4. Response Body',
        5 => '5. Full Response',
    ];

    public static array $phaseColors = [
        0 => 'alternative',
        1 => 'info',
        2 => 'warning',
        3 => 'success',
        4 => 'cyan',
        5 => 'danger',
    ];

    public static array $comparators = [
        'array' => [
            '@similar' => 'Similar',
            '@contains' => 'Contains',
        ],
        'number' => [
            '@equal' => 'Equal',
            '@greaterThan' => 'Greater Than',
            '@greaterThanOrEqual' => 'Greater Than or Equal',
            '@lessThan' => 'Less Than',
            '@lessThanOrEqual' => 'Less Than or Equal',
            '@inRange' => 'In Range',
        ],
        'string' => [
            '@mirror' => 'Mirror',
            '@startsWith' => 'Starts With',
            '@endsWith' => 'Ends With',
            '@check' => 'Check',
            '@regex' => 'Regex',
            '@checkRegex' => 'Check Regex',
        ]
    ];

    public static array $actions = [
        'allow' => 'Allow',
        'deny' => 'Deny',
        'inspect' => 'Inspect',
        'request' => 'Request',
        'setScore' => 'Set Score',
        'setLevel' => 'Set Level',
        'report' => 'Report',
    ];

    public static array $severities = [
        'notice' => 'NOTICE',
        'warning' => 'WARNING',
        'error' => 'ERROR',
        'critical' => 'CRITICAL',
    ];

    public static function name()
    {
        return [
            'nullable',
            'string',
            'max:255',
        ];
    }

    public static function alias()
    {
        return [
            'required',
            'string',
            'max:255',
            'alpha_dash',
        ];
    }

    public static function phase()
    {
        return [
            'required',
            'integer',
            'in:' . implode(',', array_keys(self::$phases)),
        ];
    }

    public static function target()
    {
        return [
            'required',
            'integer',
            fn($get) => 'in:' . implode(
                ',',
                Target::where('phase', $get('phase'))->pluck('id')->toArray()
            ),
        ];
    }
}
