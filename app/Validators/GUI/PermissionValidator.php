<?php

namespace App\Validators\GUI;

use App\Models\Permission;
use Illuminate\Validation\Rule;

class PermissionValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
            function($record)
            {
                if ($record)
                {
                    return Rule::unique('permissions', 'name')->ignore($record->id);
                }
                return 'unique:permissions,name';
            },
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
            'array',
            'exists:policies,id',
        ];
    }
}
