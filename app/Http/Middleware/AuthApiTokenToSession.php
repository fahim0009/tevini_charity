<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Laravel\Passport\Token;

class AuthApiTokenToSession
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->query('token');
        
        if ($token) {
            $hashedToken = hash('sha256', $token);
            $accessToken = Token::where('id', $hashedToken)->where('revoked', 0)->first();
            
            if ($accessToken) {
                $user = User::find($accessToken->user_id);
                if ($user) {
                    Auth::setUser($user);
                    Auth::login($user);
                }
            } else {
                Log::error('Passport Token not found or revoked: ' . $token);
            }
        }

        return $next($request);
    }
}