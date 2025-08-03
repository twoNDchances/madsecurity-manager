<?php

namespace App\Validators\API;

use App\Models\Rule;
use App\Services\TagFieldService;

class GroupValidator
{
    public static function build($required = true, $id = null)
    {
        return [
            'execution_order' => self::executionOrder($required),
            'level' => self::level($required),
            'name' => self::name($required, $id),
            'rule_ids' => self::ruleIds(),
            'rule_ids.*' => self::ruleId(),
            'defender_ids' => self::defenderIds(),
            'defender_ids.*' => self::defenderId(),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'description' => self::description(),
        ];
    }

    private static function executionOrder($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|integer|min:1';
    }

    private static function level($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|integer|min:1|max:999999999';
    }

    private static function name($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|alpha_dash|unique:groups,name' . ($id ? ",$id" : '');
    }

    private static function ruleIds()
    {
        return [
            'nullable',
            'array',
            function($attribute, $value, $fail)
            {
                $phase = null;
                foreach ($value as $id)
                {
                    $rule = Rule::find($id);
                    if (!$rule)
                    {
                        continue;
                    }
                    if (is_null($phase))
                    {
                        $phase = $rule->phase;
                        continue;
                    }
                    if (in_array($phase, [0, 1, 2]))
                    {
                        if (!in_array($rule->phase, [0, 1, 2]))
                        {
                            $fail("The {$attribute} has 2 different phases for Request");
                            break;
                        }
                    }
                    else if (in_array($phase, [3, 4, 5]))
                    {
                        if (!in_array($rule->phase, [3, 4, 5]))
                        {
                            $fail("The {$attribute} has 2 different phases for Response");
                            break;
                        }
                    }
                }
            },
        ];
    }

    private static function ruleId()
    {
        return 'exists:rules,id';
    }

    private static function defenderIds()
    {
        return 'nullable|array';
    }

    private static function defenderId()
    {
        return 'exists:defenders,id';
    }

    private static function description()
    {
        return 'nullable|string';
    }
}
