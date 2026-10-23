<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StripeConfigController extends Controller
{
    /**
     * Return Stripe publishable key and mode (test/live)
     * No authentication required - publishable key is meant to be public
     */
    public function getStripeConfig()
    {
        $publishableKey = env('STRIPE_KEY');
        $secretKey = env('STRIPE_SECRET');
        
        // Determine mode from secret key prefix
        $mode = 'test';
        if ($secretKey && str_starts_with($secretKey, 'sk_live')) {
            $mode = 'live';
        }
        if ($publishableKey && str_starts_with($publishableKey, 'pk_live')) {
            $mode = 'live';
        }

        return response()->json([
            'success' => true,
            'response' => [
                'publishable_key' => $publishableKey,
                'mode' => $mode,
            ]
        ], 200);
    }
}