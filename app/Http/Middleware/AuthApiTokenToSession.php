<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Passport\Token as PersonalAccessToken;

class AuthApiTokenToSession
{
    public function handle(Request $request, Closure $next)
    {
        // Flutter theke token URL e 'token' parameter hishebe asbe
        $token = $request->query('token');
        
        if ($token) {
            // Token ta valid kina check korbe
            $accessToken = PersonalAccessToken::findToken($token);
            
            if ($accessToken) {
                $user = $accessToken->tokenable;
                if ($user) {
                    // Web session e user login kore dibe
                    Auth::login($user);
                }
            }
        }

        return $next($request);
    }
}