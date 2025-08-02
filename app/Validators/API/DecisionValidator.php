<?php

namespace App\Validators\API;

use App\Services\TagFieldService;

class DecisionValidator
{
    public static function build($request, $required = true, $id = null)
    {
        return [
            'score' => self::score($required),
            'phase_type' => self::phaseType($required),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'name' => self::name($required, $id),
            'defender_ids' => self::defenderIds(),
            'defender_ids.*' => self::defenderId(),
            'action' => self::action($request, $required),
            'redirect' => self::redirect(),
            'kill_header' => self::killHeader(),
            'kill_path' => self::killPath(),
            'wordlist_id' => self::wordlistId(),
            'description' => self::description(),
        ];
    }

    private static $phaseTypes = [
        'request' => 'Request',
        'response' => 'Response',
    ];

    private static $actions = [
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

    private static function score($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|integer|min:-999999999|max:999999999';
    }

    private static function phaseType($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|string|in:' . implode(
            ',',
            array_keys(self::$phaseTypes),
        );
    }

    private static function name($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255|unique:decisions,name' . ($id ? ",$id" : '');
    }

    private static function defenderIds()
    {
        return 'nullable|array';
    }

    private static function defenderId()
    {
        return 'exists:defenders,id';
    }

    private static function action($request, $required = true)
    {
        return [
            $required ? 'required' : 'sometimes',
            'string',
            function ($attribute, $value, $fail) use ($request)
            {
                $phaseType = $request->input('phase_type');
                if (!isset(self::$actions[$phaseType]))
                {
                    $fail("Invalid $attribute selected.");
                    return;
                }
                if (!array_key_exists($value, self::$actions[$phaseType]))
                {
                    $fail("The selected action '$value' is not valid for Phase Type '$phaseType'.");
                    return;
                }
            },
        ];
    }

    private static function redirect()
    {
        return 'required_if:action,redirect|string|url';
    }

    private static function killHeader()
    {
        return 'required_if:action,kill|string|max:255';
    }

    private static function killPath()
    {
        return 'required_if:action,kill|string|starts_with:/';
    }

    private static function wordlistId()
    {
        return 'required_if:action,tag,warn|integer|exists:wordlists,id';
    }

    private static function description()
    {
        return 'nullable|string';
    }
}
