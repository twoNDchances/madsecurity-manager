<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->getUser();
        $password = $request->getPassword();
        $tokenHeader = $request->header(env('MANAGER_TOKEN_KEY', 'X-Manager-Token'));
        if (!$email || !$password || !$tokenHeader)
        {
            abort(404);
        }
        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password))
        {
            abort(404);
        }
        $abort = true;
        foreach ($user->tokens()->get() as $token)
        {
            if (Hash::check($tokenHeader, $token->value))
            {
                if ($token->expired_at && Carbon::parse($token->expired_at)->isPast())
                {
                    abort(404);
                }
                else
                {
                    $abort = false;
                }
                break;
            }
        }
        if ($abort)
        {
            abort(404);
        }
        Auth::setUser($user);
        return $next($request);
    }
}
