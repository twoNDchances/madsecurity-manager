<?php

namespace App\Validators\API;

use App\Models\Target;
use App\Services\TagFieldService;

class RuleValidator
{
    public static function build($request, $required = true, $id = null)
    {
        return [
            'name' => self::name(),
            'alias' => self::alias($required, $id),
            'phase' => self::phase($required),
            'target_id' => self::targetId($request, $required),
            'comparator' => self::comparator($request, $required),
            'inverse' => self::inverse($required),
            'value' => self::value($request),
            'from' => self::specificNumber(),
            'to' => self::specificNumber(),
            'wordlist_id' => self::wordlistId(),
            'action' => self::action(),
            'severity' => self::severity(),
            'request_method' => self::requestMethod(),
            'request_url' => self::requestURL(),
            'score' => self::score(),
            'level' => self::level(),
            'variable_key' => self::setKey('Variable'),
            'variable_value' => self::setValue('Variable'),
            'header_key' => self::setKey('Header'),
            'header_value' => self::setValue('Header'),
            'group_ids' => self::groupIds(),
            'group_ids.*' => self::groupId(),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'description' => self::description(),
            'log' => self::logistic($required),
            'time' => self::logistic($required),
            'user_agent' => self::logistic($required),
            'client_ip' => self::logistic($required),
            'method' => self::logistic($required),
            'path' => self::logistic($required),
            'output' => self::logistic($required),
            'target' => self::logistic($required),
            'rule' => self::logistic($required),
        ];
    }

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
        'record' => 'Record',
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
        return ($required ? 'required' : 'sometimes') . '|integer|in:' . implode(',', array_keys(self::$phases));
    }

    private static function targetId($request, $required = true)
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
                    $fail("Invalid $attribute selected for Phase '$phase'.");
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
                $finalDatatype = Target::find($targetId)?->final_datatype;
                if ($finalDatatype)
                {
                    if (!in_array($value, array_keys(self::$comparators[$finalDatatype])))
                    {
                        $fail("Invalid $attribute selected for Final Datatype '$finalDatatype'.");
                        return;
                    }
                }
            },
        ];
    }

    private static function inverse($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|boolean';
    }

    private static function value($request)
    {
        $comparator = $request->input('comparator');
        if (in_array($comparator, [
            '@equal',
            '@lessThan',
            '@greaterThan',
            '@lessThanOrEqual',
            '@greaterThanOrEqual',
        ]))
        {
            return 'required_if:comparator,@equal,@lessThan,@greaterThan,@lessThanOrEqual,@greaterThanOrEqual|numeric';
        }
        if (!in_array($comparator, [
            '@similar',
            '@search',
            '@check',
            '@checkRegex',
            '@inRange',
        ]))
        {
            return 'required_unless:comparator,@similar,@search,@check,@checkRegex,@inRange|string';
        }
        return 'nullable';
    }

    private static function anyNumber()
    {
        return ;
    }

    private static function specificNumber()
    {
        return 'required_if:comparator,@inRange|numeric';
    }

    private static function wordlistId()
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
