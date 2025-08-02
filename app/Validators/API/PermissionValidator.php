<?php

namespace App\Validators\API;

use App\Models\Permission;

class PermissionValidator
{
    public static function build($required = true, $id = null)
    {
        return [
            'name' => self::name($required, $id),
            'action' => self::action($required),
            'description' => self::description(),
            'policy_ids' => self::policyIds(),
            'policy_ids.*' => self::policyId(),
        ];
    }

    private static function name($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|alpha_dash|unique:permissions,name' . ($id ? ",$id" : '');
    }

    private static function action($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|in:' . implode(
            ',',
            array_keys(Permission::getAvailablePermissions()),
        );
    }

    private static function description()
    {
        return 'nullable|string';
    }

    private static function policyIds()
    {
        return 'nullable|array';
    }

    private static function policyId()
    {
        return 'exists:policies,id';
    }
}
