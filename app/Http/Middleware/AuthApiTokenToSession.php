<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthApiTokenToSession
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->query('token');
        
        if ($token) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
            
            $user = Auth::guard('api')->user();
            
            if ($user) {
                Auth::login($user);
                Log::info('getMakeDonation (Middleware) called for user ID: ' . $user->id);
            } else {
                Log::error('Passport JWT Authentication failed for token: ' . $token);
            }
        }

        return $next($request);
    }
}