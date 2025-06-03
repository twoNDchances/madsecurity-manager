<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthenticationService
{
    public static function get()
    {
        return User::find(Auth::user()?->id);
    }

    public static function can(User $user, string $resource, string $action)
    {
        if (!$user)
        {
            return false;
        }
        if ($user->important)
        {
            return true;
        }
        if ($user->hasPermission("$resource.all"))
        {
            return true;
        }
        return $user->hasPermission("$resource.$action");
    }
}
