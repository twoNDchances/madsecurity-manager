<?php

namespace App\Validators\API;

use App\Services\TagFieldService;

class UserValidator
{
    public static function build($required = true, $id = null)
    {
        return [
            'name' => self::name($required),
            'email' => self::email($required, $id),
            'password' => self::password($required),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'verification' => self::verification($required),
            'policy_ids' => self::policyIds(),
            'policy_ids.*' => self::policyId(),
            'activation' => self::activation($required),
            'important' => self::important($required),
            'token_ids' => self::tokenIds(),
            'token_ids.*' => self::tokenId(),
        ];
    }

    private static function name($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255';
    }

    private static function email($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|email|max:255|unique:users,email' . ($id ? ",$id" : '');
    }

    private static function password($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|string|min:4|max:255';
    }

    private static function verification($required = true)
    {
        return $required ? 'required|boolean' : 'nullable';
    }

    private static function policyIds()
    {
        return 'nullable|array';
    }

    private static function policyId()
    {
        return 'exists:policies,id';
    }

    private static function activation($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|boolean';
    }

    private static function important($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|boolean';
    }

    private static function tokenIds()
    {
        return 'nullable|array';
    }

    private static function tokenId()
    {
        return 'exists:tokens,id';
    }
}
