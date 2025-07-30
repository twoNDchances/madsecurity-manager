<?php

namespace App\Http\Middleware;

use App\Services\AuthenticationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthDefenderCollect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $query = $request->query('id');
        if (!$query)
        {
            abort(404);
        }
        $user = AuthenticationService::get();
        if (!$user)
        {
            abort(404);
        }
        $can = AuthenticationService::can($user, 'defender', 'collect');
        if (!$can)
        {
            abort(404);
        }
        return $next($request);
    }
}
