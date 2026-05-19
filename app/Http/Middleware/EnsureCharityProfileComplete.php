<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EnsureCharityProfileComplete
{
    public function handle(Request $request, Closure $next)
    {
        // If logged in and profile is incomplete, share a variable with all views
        if (auth('charity')->check() && !auth('charity')->user()->isProfileComplete()) {
            View::share('profileIncomplete', true);
        }

        return $next($request); // Always let them continue to the page
    }
}