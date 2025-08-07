<?php

namespace App\Validators\API;

use App\Models\Target;

class RuleValidator
{
    private static array $phases = [
        0 => '0. Full Request',
        1 => '1. Request Header',
        2 => '2. Request Body',
        3 => '3. Response Header',
        4 => '4. Response Body',
        5 => '5. Full Response',
    ];

    private static array $comparators = [
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

    private static array $actions = [
        'allow' => 'Allow',
        'deny' => 'Deny',
        'inspect' => 'Inspect',
        'request' => 'Request',
        'setScore' => 'Set Score',
        'setLevel' => 'Set Level',
        'report' => 'Report',
        'setVariable' => 'Set Variable',
        'setHeader' => 'Set Header',
    ];

    private static array $requestMethods = [
        'post' => 'POST',
        'patch' => 'PATCH',
        'put' => 'PUT',
        'delete' => 'DELETE',
    ];

    private static array $severities = [
        'notice' => 'NOTICE',
        'warning' => 'WARNING',
        'error' => 'ERROR',
        'critical' => 'CRITICAL',
    ];

    private static function name()
    {
        return 'nullable|string|max:255';
    }

    private static function alias($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255|alpha_dash|unique:rules,name' . ($id ? ",$id" : '');
    }

    private static function phase($required = true)
    {
        return ($required ? 'required' : 'sometimes') . 'integer|in:' . implode(',', array_keys(self::$phases));
    }

    private static function target($request, $required = true)
    {
        return [
            ($required ? 'required' : 'sometimes'),
            'integer',
            function ($attribute, $value, $fail) use ($request)
            {
                $phase = (int) $request->input('phase');
                if (!isset(self::$phases[$phase]))
                {
                    $fail("Invalid $attribute selected.");
                    return;
                }
                if (!in_array($value, Target::where('phase', $phase)->pluck('id')->toArray()))
                {
                    $fail("Invalid $attribute selected for phase '$phase'.");
                    return;
                }
            },
        ];
    }

    private static function comparator($request, $required = true)
    {
        return [
            ($required ? 'required' : 'sometimes'),
            'string',
            'starts_with:@',
            function ($attribute, $value, $fail) use ($request)
            {
                $targetId = (int) $request->input('target_id');
            },
            // function($get)
            // {
            //     $finalDatatype = Target::find($get('target_id'))?->final_datatype;
            //     $rule = null;
            //     if ($finalDatatype)
            //     {
            //         $rule = 'in:' . implode(
            //             ',',
            //             array_keys(self::$comparators[$finalDatatype])
            //         );
            //     }
            //     return $rule;
            // }
        ];
    }

    private static function inverse($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|boolean';
    }

    private static function value()
    {
        return 'required_unless:comparator,@similar,@search,@check,@checkRegex,@inRange|string';
    }

    private static function anyNumber()
    {
        return 'required_if:comparator,@equal,@lessThan,@greaterThan,@lessThanOrEqual,@greaterThanOrEqual|numeric';
    }

    private static function specificNumber()
    {
        return 'required_if:comparator,@inRange|numeric';
    }

    private static function wordlist()
    {
        return 'required_if:comparator,@similar,@search,@check,@checkRegex|integer|exists:wordlists,id';
    }

    private static function action()
    {
        return 'nullable|string|in:' . implode(
            ',',
            array_keys(self::$actions),
        );
    }

    private static function severity()
    {
        return 'required_if:action,inspect|string|in:' . implode(
            ',',
            array_keys(self::$severities),
        );
    }

    private static function requestMethod()
    {
        return 'required_if:action,request|string|in:' . implode(
            ',',
            array_keys(self::$requestMethods),
        );
    }

    private static function requestURL()
    {
        return 'required_if:action,request|string|url';
    }

    private static function score()
    {
        return 'required_if:action,setScore|integer|min:-999999999|max:999999999';
    }

    private static function level()
    {
        return 'required_if:action,setScore|integer|min:1|max:999999999';
    }

    private static function setKey($for)
    {
        return "required_if:action,set$for|string|max:255|alpha_dash";
    }

    private static function setValue($for)
    {
        return "required_if:action,set$for|string|max:255";
    }

    private static function groupIds()
    {
        return 'nullable|array|exists:groups,id';
    }

    private static function groupId()
    {
        return 'exists:groups,id';
    }

    private static function description()
    {
        return 'nullable|string';
    }

    private static function logistic($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|boolean';
    }
}
