<?php

namespace App\Validators\API;

use App\Services\TagFieldService;

class DefenderValidator
{
    public static function build($required = true, $id = null)
    {
        return [
            'name' => self::name($required),
            'group_ids' => self::groupIds(),
            'group_ids.*' => self::groupId(),
            'url' => self::url($required, $id),
            'health' => self::path($required),
            'health_method' => self::method($required),
            'apply' => self::path($required),
            'apply_method' => self::method($required),
            'revoke' => self::path($required),
            'revoke_method' => self::method($required),
            'implement' => self::path($required),
            'implement_method' => self::method($required),
            'suspend' => self::path($required),
            'suspend_method' => self::method($required),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'description' => self::description(),
            'important' => self::important($required),
            'periodic' => self::periodic($required),
            'protection' => self::protection($required),
            'username' => self::username(),
            'password' => self::password(),
            'decision_ids' => self::decisionIds(),
            'decision_ids.*' => self::decisionId(),
        ];
    }

    private static array $methods = [
        'post' => 'POST',
        'put' => 'PUT',
        'patch' => 'PATCH',
        'delete' => 'DELETE',
    ];

    private static function name($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255';
    }

    private static function groupIds()
    {
        return 'nullable|array';
    }

    private static function groupId()
    {
        return 'exists:groups,id';
    }

    private static function url($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|url|unique:defenders,url' . ($id ? ",$id" : '');
    }

    private static function path($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|string|starts_with:/';
    }

    private static function method($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|string|in:' . implode(
            ',',
            array_keys(self::$methods),
        );
    }

    private static function description()
    {
        return 'nullable|string';
    }

    private static function important($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|boolean';
    }

    private static function periodic($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|boolean';
    }

    private static function protection($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|boolean';
    }

    private static function username()
    {
        return 'required_if:protection,true|string|max:255';
    }

    private static function password()
    {
        return 'required_if:protection,true|string|min:8|max:255';
    }

    private static function decisionIds()
    {
        return 'nullable|array';
    }

    private static function decisionId()
    {
        return 'exists:decisions,id';
    }
}
