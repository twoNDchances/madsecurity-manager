<?php

namespace App\Validators\API;

use App\Services\TagFieldService;

class TokenValidator
{
    public static function build($required = true, $id = null)
    {
        return [
            'name' => self::name($required, $id),
            'expired_at' => self::expiredAt(),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'description' => self::description(),
            'user_ids' => self::userIds(),
            'user_ids.*' => self::userId(),
        ];
    }

    private static function name($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255|unique:tokens,name' . ($id ? ",$id" : '');
    }

    private static function description()
    {
        return 'nullable|string';
    }

    private static function expiredAt()
    {
        return 'nullable|date';
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
