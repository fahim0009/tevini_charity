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

        'http://127.0.0.1:8000/card-enrol-fingerprint',
        'http://127.0.0.1:8000/card-fingerprint-donation',
        'http://127.0.0.1:8000/card-is-fingerprint-user-enrolled',
        'http://127.0.0.1:8000/card-deregister-fingerprint',

        'http://127.0.0.1:8000/transaction-store',
        'http://127.0.0.1:8000/api/transaction-store'
        
    ];
}
