<?php

namespace App\Http\Middleware;

use App\Services\IdentificationService;
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
        $can = IdentificationService::can(
            IdentificationService::get(),
            'defender',
            'collect',
        );
        if (!$can)
        {
            abort(404);
        }
        return $next($request);
    }
}
