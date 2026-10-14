<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthApiTokenToSession
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->query('token');
        
        if ($token) {
            // ১. Sanctum দিয়ে চেক করা
            if (class_exists(\Laravel\Sanctum\PersonalAccessToken::class)) {
                $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($accessToken) {
                    $user = $accessToken->tokenable;
                    if ($user) {
                        Auth::login($user);
                    }
                }
            }
            
            // ২. যদি Sanctum না থাকে বা কাজ না করে, তবে Passport দিয়ে চেক করা
            if (!Auth::check() && class_exists(\Laravel\Passport\Token::class)) {
                $hashedToken = hash('sha256', $token);
                $passportToken = \Laravel\Passport\Token::where('id', $hashedToken)->where('revoked', 0)->first();
                if ($passportToken) {
                    $user = User::find($passportToken->user_id);
                    if ($user) {
                        Auth::login($user);
                    }
                }
            }
        }

        return $next($request);
    }
}