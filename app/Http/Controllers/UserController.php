<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function verify($token)
    {
        $user = User::where('token', $token)->first();
        if (!$user || $user->email_verified_at)
        {
            abort(404);
        }
        $user->update([
            'token' => null,
            'email_verified_at' => now(),
        ]);
        Auth::login($user);
        return redirect()->route('filament.manager.home');
    }
}
