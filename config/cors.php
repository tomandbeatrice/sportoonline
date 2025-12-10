<?php

return [
  'paths' => ['api/*', 'sanctum/csrf-cookie', 'storage/*'],
  'allowed_methods' => ['*'],
  // Use environment variable for production domains
  'allowed_origins' => array_filter(array_merge(
    // Development origins
    [
      'http://127.0.0.1:5173',
      'http://localhost:5173',
      'http://localhost:3000',
      'http://127.0.0.1:3000',
    ],
    // Production origins from environment
    explode(',', env('CORS_ALLOWED_ORIGINS', ''))
  )),
  'allowed_origins_patterns' => [],
  'allowed_headers' => ['*'],
  'exposed_headers' => ['Content-Disposition'],
  'max_age' => 86400,
  'supports_credentials' => true,
];