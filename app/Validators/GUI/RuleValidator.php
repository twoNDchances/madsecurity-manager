<?php

namespace App\Validators\GUI;

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

    public static array $comparators = [
        'array' => [
            '@similar' => 'Similar',
            '@contains' => 'Contains',
            '@match' => 'Match',
            '@search' => 'Search',
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
        'suspect' => 'Suspect',
        'request' => 'Request',
        'setScore' => 'Set Score',
        'setLevel' => 'Set Level',
        'report' => 'Report',
        'setVariable' => 'Set Variable',
        'setHeader' => 'Set Header',
    ];

    public static array $requestMethods = [
        'post' => 'POST',
        'patch' => 'PATCH',
        'put' => 'PUT',
        'delete' => 'DELETE',
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
            function($record)
            {
                if ($record)
                {
                    return "unique:rules,alias,$record->id";
                }
                return 'unique:rules,alias';
            },
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

    public static function comparator()
    {
        return [
            'required',
            'string',
            'starts_with:@',
            function($get)
            {
                $finalDatatype = Target::find($get('target_id'))?->final_datatype;
                $rule = null;
                if ($finalDatatype)
                {
                    $rule = 'in:' . implode(
                        ',',
                        array_keys(self::$comparators[$finalDatatype])
                    );
                }
                return $rule;
            }
        ];
    }

    public static function inverse()
    {
        return [
            'required',
            'boolean',
        ];
    }

    public static function value()
    {
        return [
            'required_unless:comparator,@similar,@search,@check,@checkRegex,@inRange',
            'string',
        ];
    }

    public static function anyNumber()
    {
        return [
            'required_if:comparator,@equal,@lessThan,@greaterThan,@lessThanOrEqual,@greaterThanOrEqual',
            'numeric',
        ];
    }

    public static function specificNumber()
    {
        return [
            'required_if:comparator,@inRange',
            'numeric',
        ];
    }

    public static function wordlist()
    {
        return [
            'required_if:comparator,@similar,@search,@check,@checkRegex',
            'integer',
            'exists:wordlists,id',
        ];
    }

    public static function action()
    {
        return [
            'nullable',
            'string',
            'in:' . implode(
                ',',
                array_keys(self::$actions),
            ),
        ];
    }

    public static function severity()
    {
        return [
            'required_if:action,suspect',
            'string',
            'in:' . implode(
                ',',
                array_keys(self::$severities),
            )
        ];
    }

    public static function requestMethod()
    {
        return [
            'required_if:action,request',
            'string',
            'in:' . implode(
                ',',
                array_keys(self::$requestMethods),
            ),
        ];
    }

    public static function requestURL()
    {
        return [
            'required_if:action,request',
            'string',
            'url',
        ];
    }

    public static function score()
    {
        return [
            'required_if:action,setScore',
            'integer',
            'min:-999999999',
            'max:999999999',
        ];
    }

    public static function level()
    {
        return [
            'required_if:action,setScore',
            'integer',
            'min:1',
            'max:999999999',
        ];
    }

    public static function setKey($for)
    {
        return [
            "required_if:action,set$for",
            'string',
            'max:255',
            'alpha_dash',
        ];
    }

    public static function setValue($for)
    {
        return [
            "required_if:action,set$for",
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

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function logistic()
    {
        return [
            'required',
            'boolean',
        ];
    }
}
