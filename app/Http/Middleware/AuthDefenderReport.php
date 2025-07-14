<?php

namespace App\Http\Middleware;

use App\Models\Defender;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthDefenderReport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isJson())
        {
            abort(404);
        }
        $data = $request->json()->all();
        if (!array_key_exists('auth', $data) || !is_array($data['auth']))
        {
            abort(404);
        }
        foreach (['id', 'username', 'password'] as $field)
        {
            if (!array_key_exists($field, $data['auth']))
            {
                abort(404);
            }
        }
        foreach (['time', 'output', 'user_agent', 'client_ip', 'method', 'path', 'target_ids', 'rule_id'] as $field)
        {
            if (!array_key_exists($field, $data['data']))
            {
                abort(404);
            }
        }
        $defender = Defender::where([
            ['id', '=', $data['auth']['id']],
            ['username', '=', $data['auth']['username']],
            ['password', '=', $data['auth']['password']],
        ])
        ->first();
        if (!$defender)
        {
            abort(404);
        }
        return $next($request);
    }
}
