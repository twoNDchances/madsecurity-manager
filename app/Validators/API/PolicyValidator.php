<?php

namespace App\Validators\API;

use App\Services\TagFieldService;

class PolicyValidator
{
    public static function build($required = true, $id = null)
    {
        return [
            'name' => self::name($required, $id),
            'permission_ids' => self::permissionIds(),
            'permission_ids.*' => self::permissionId(),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'description' => self::description(),
            'user_ids' => self::userIds(),
            'user_ids.*' => self::userId(),
        ];
    }

    private static function name($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255|unique:policies,name' . ($id ? ",$id" : '');
    }

    private static function permissionIds()
    {
        return 'nullable|array';
    }

    private static function permissionId()
    {
        return 'exists:permissions,id';
    }

    private static function description()
    {
        return 'nullable|string';
    }

    private static function userIds()
    {
        return 'nullable|array';
    }

    private static function userId()
    {
        return 'exists:users,id';
    }
}
