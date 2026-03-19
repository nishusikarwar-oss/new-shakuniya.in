<?php

// config/cors.php
// ============================================================
// WHAT CHANGED:
//   - allowed_origins now includes your Next.js dev server
//   - supports_credentials is true (required for Sanctum SPA)
//   - Add your production domain to allowed_origins_patterns
// ============================================================

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',   // Next.js dev server
        'http://127.0.0.1:3000',
        // Add your production URL here, e.g.:
        // 'https://yourdomain.com',
        // 'https://admin.yourdomain.com',
    ],

    /*
     * If you deploy to a wildcard subdomain, use patterns instead:
     *   'allowed_origins_patterns' => ['^https:\/\/.*\.yourdomain\.com$'],
     */
    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    /*
     * IMPORTANT: must be true for Laravel Sanctum cookie-based auth.
     * Make sure SESSION_DOMAIN and SANCTUM_STATEFUL_DOMAINS are set in .env
     */
    'supports_credentials' => true,
];
