<?php

namespace App\Validators;

use App\Models\Rule as ModelsRule;
use Illuminate\Validation\Rule;

class GroupValidator
{
    public static function executionOrder()
    {
        return [
            'required',
            'integer',
            'min:1',
        ];
    }

    public static function level()
    {
        return [
            'required',
            'integer',
            'min:1',
            'max:999999999',
        ];
    }

    public static function name()
    {
        return [
            'required',
            'string',
            'alpha_dash',
            function($record)
            {
                if ($record)
                {
                    return Rule::unique('groups', 'name')->ignore($record->id);
                }
                return 'unique:groups,name';
            },
        ];
    }

    public static function rules()
    {
        return [
            'nullable',
            'array',
            'exists:rules,id',
            fn() => function($attribute, $value, $fail)
            {
                $phase = null;
                foreach ($value as $id)
                {
                    $rule = ModelsRule::find($id);
                    if (!$rule)
                    {
                        continue;
                    }
                    if (is_null($phase))
                    {
                        $phase = $rule->phase;
                        continue;
                    }
                    if ($rule->phase != $phase)
                    {
                        $fail("The {$attribute} has 2 different phases");
                        break;
                    }
                }
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

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }
}
