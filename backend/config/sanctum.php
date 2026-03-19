<?php

// config/sanctum.php
// =====================================================================
// WHAT CHANGED:
//   - stateful: added localhost:3000 for Next.js SPA auth
//   - expiration: set to 1440 min (24 h); null = never expire
// =====================================================================

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    | Requests from these domains will use cookie-based auth.
    | Add your production domain here alongside localhost.
    |--------------------------------------------------------------------------
    */
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', implode(',', [
        'localhost',
        'localhost:3000',
        '127.0.0.1',
        '127.0.0.1:3000',
        '127.0.0.1:8000',
        // 'yourdomain.com',
        // 'admin.yourdomain.com',
    ]))),

    'guard' => ['web'],

    /*
     * Token expiration in minutes.  null = tokens never expire.
     * 1440 = 24 hours.
     */
    'expiration' => 1440,

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies'      => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token'  => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
