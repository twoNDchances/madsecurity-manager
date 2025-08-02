<?php

namespace App\Http\Middleware;

use App\Services\IdentificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthCapabilityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = IdentificationService::get();
        if (!$user)
        {
            abort(404);
        }
        $path = $request->path();
        $components = explode('/', $path);
        if (count($components) < 4)
        {
            abort(404);
        }
        $resource = Str::singular($components[2]);
        $action = match ($components[3])
        {
            'list' => 'viewAny',
            'show' => 'view',
            default => $components[3],
        };
        $can = IdentificationService::can($user, $resource, $action);
        if (!$can)
        {
            abort(404);
        }
        return $next($request);
    }
}
