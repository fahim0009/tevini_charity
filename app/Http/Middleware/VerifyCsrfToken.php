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

        'https://www.tevini.co.uk/card-enrol-fingerprint',
        'https://www.tevini.co.uk/card-fingerprint-donation',
        'https://www.tevini.co.uk/card-is-fingerprint-user-enrolled',
        'https://www.tevini.co.uk/card-deregister-fingerprint',

        'http://127.0.0.1:8000/transaction-store',
        'http://127.0.0.1:8000/api/transaction-store'
        
    ];
}
