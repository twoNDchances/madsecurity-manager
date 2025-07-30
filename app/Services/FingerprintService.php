<?php

namespace App\Services;

use App\Models\Fingerprint;
use Illuminate\Support\Str;

class FingerprintService
{
    public static function generate($resource, $action)
    {
        $user = AuthenticationService::get();
        return Fingerprint::create([
            'user_id' => $user->id,
            'ip_address' => request()->getClientIp(),
            'user_agent' => request()->userAgent(),
            'http_method' => request()->method(),
            'route' => request()->path(),
            'action' => $action,
            'resource_type' => $resource::class,
            'resource_id' => $resource->id,
        ]);
    }

    public static function controlObserver($resource, $action)
    {
        if ($resource::$skipObserver)
        {
            return;
        }
        self::generate($resource, $action);
    }
}
