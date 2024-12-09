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
        'https://www.tevini.co.uk/api',
        'https://www.tevini.co.uk/api/transaction-store',
        'https://www.tevini.co.uk/transaction-store',

        'https://www.tevini.co.uk/cardEnrolFingerprint',
        'https://www.tevini.co.uk/cardFingerprintDonation',
        'https://www.tevini.co.uk/cardIsFingerprintUserEnrolled',
        'https://www.tevini.co.uk/cardDeregisterFingerprint',

        
        'https://www.tevini.co.uk/api/make-donation',
        'https://www.tevini.co.uk/make-online-donation',
        'https://www.tevini.co.uk/user/make-donation',
        'https://www.tevini.co.uk/user/standing-donation',

        'http://127.0.0.1:8000/all-donor',
        'https://www.tevini.co.uk/app-version',
        'http://127.0.0.1:8000/transaction-store',
        'http://127.0.0.1:8000/api/transaction-store'
        
    ];
}
