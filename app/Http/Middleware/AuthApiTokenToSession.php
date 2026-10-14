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
            // URL থেকে আসা টোকেনটি Authorization Header এ বসিয়ে দিচ্ছি
            $request->headers->set('Authorization', 'Bearer ' . $token);
            
            // Passport এর 'api' guard দিয়ে ইউজারকে যাচাই করে বের করা হচ্ছে
            $user = Auth::guard('api')->user();
            
            if ($user) {
                // ওয়েব সেশনে ইউজারকে লগইন করিয়ে দিচ্ছি
                Auth::login($user);
                Log::info('getMakeDonation (Middleware) called for user ID: ' . $user->id);
            } else {
                Log::error('Passport JWT Authentication failed for token: ' . $token);
            }
        }

        return $next($request);
    }
}