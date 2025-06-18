<?php

namespace App\Validators;

use App\Models\Permission;

class PermissionValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    public static function action()
    {
        $options = Permission::getAvailablePermissions();
        return [
            'required',
            'in:' . implode(',', array_keys($options)),
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function policies()
    {
        return [
            'nullable',
            'exists:policies,id',
        ];
    }
}
