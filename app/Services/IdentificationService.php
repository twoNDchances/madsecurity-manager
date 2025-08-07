<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class IdentificationService
{
    public static function get()
    {
        return User::find(Auth::user()?->id);
    }

    public static function can(User $user, string $resource, string $action)
    {
        if (!$user || !$user->email_verified_at || !$user->active)
        {
            return false;
        }
        if ($user->important || $user->hasPermission("$resource.all"))
        {
            return true;
        }
        return $user->hasPermission("$resource.$action");
    }

    public static function render(array $widgets)
    {
        $user = self::get();
        if (!$user || !$user->email_verified_at)
        {
            NotificationService::notify(
                'failure',
                'Unverified account',
                'This account needs to be verified before it can be used.'
            );
            return [];
        }

        if (!$user || !$user->active)
        {
            NotificationService::notify(
                'failure',
                'Unactivated account',
                'Account needs to be activated to work.'
            );
            return [];
        }
        return $widgets;
    }

    public static function load(&$resource, $relationships)
    {
        $user = self::get();
        if (!$user)
        {
            return;
        }
        $fields = [];
        foreach ($relationships as $name => $field)
        {
            if (self::can($user, $name, 'view'))
            {
                if (is_string($field))
                {
                    $fields[] = $field;
                }
                if (is_array($field))
                {
                    $fields = array_merge($fields, $field);
                }
            }
        }
        if (count($fields) > 0)
        {
            $resource->load($fields);
        }
    }

    public static function important()
    {
        $user = self::get();
        return function($query) use ($user)
        {
            if (!$user->important)
            {
                $query = $query->where('important', false);
            }
            return $query;
        };
    }
}
