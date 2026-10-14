<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Passport\Token;

class AuthApiTokenToSession
{
    public function handle(Request $request, Closure $next)
    {
        // Flutter theke token URL e 'token' parameter hishebe asbe
        $token = $request->query('token');
        
        if ($token) {
            // Passport token gulo hashed (sha256) shape e database e save thake
            $hashedToken = hash('sha256', $token);
            
            // oauth_access_tokens table theke token check korbe
            $accessToken = Token::where('id', $hashedToken)->where('revoked', 0)->first();
            
            if ($accessToken) {
                // Token er user_id diye user ke khuje ber korbo
                $user = User::find($accessToken->user_id);
                if ($user) {
                    // Web session e user login kore dibe
                    Auth::login($user);
                }
            }
        }

        return $next($request);
    }
}