<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Web URLs (without domain name)
        'api/transaction-store',
        'transaction-store',

        'cardEnrolFingerprint',
        'cardFingerprintDonation',
        'cardIsFingerprintUserEnrolled',
        'cardDeregisterFingerprint',

        // API Donation Routes for App
        'api/make-donation',
        'make-online-donation',
        'api/make-online-donation',
        'user/make-donation',
        'user/standing-donation',

        'all-donor',
        'app-version',

        // OneGiv Webhooks
        'api/webhooks/onegiv/*',
    ];
}