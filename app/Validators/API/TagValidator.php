<?php

namespace App\Validators\API;

class TagValidator
{
    public static function build($required = true, $id = null)
    {
        return [
            'name' => self::name($required, $id),
            'color' => self::color($required),
            'description' => self::description(),
        ];
    }

    public static function name($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255|unique:tags,name' . ($id ? ",$id" : '');
    }

    public static function color($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|string|size:7|starts_with:#';
    }

    private static function description()
    {
        return 'nullable|string';
    }
}
