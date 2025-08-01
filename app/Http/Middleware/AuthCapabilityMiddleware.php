<?php

namespace App\Http\Middleware;

use App\Services\AuthenticationService;
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
        $user = AuthenticationService::get();
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
        $can = AuthenticationService::can($user, $resource, $components[3]);
        if (!$can)
        {
            abort(404);
        }
        return $next($request);
    }
}
