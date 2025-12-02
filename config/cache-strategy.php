<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Cache strategies for different parts of the application
    |
    */

    'products' => [
        'ttl' => env('CACHE_PRODUCTS_TTL', 3600), // 1 hour
        'tags' => ['products'],
    ],

    'categories' => [
        'ttl' => env('CACHE_CATEGORIES_TTL', 7200), // 2 hours
        'tags' => ['categories'],
    ],

    'sellers' => [
        'ttl' => env('CACHE_SELLERS_TTL', 1800), // 30 minutes
        'tags' => ['sellers'],
    ],

    'campaigns' => [
        'ttl' => env('CACHE_CAMPAIGNS_TTL', 900), // 15 minutes
        'tags' => ['campaigns'],
    ],

    'routes' => [
        'enabled' => env('CACHE_ROUTES_ENABLED', true),
    ],

    'config' => [
        'enabled' => env('CACHE_CONFIG_ENABLED', true),
    ],

    'views' => [
        'enabled' => env('CACHE_VIEWS_ENABLED', true),
    ],
];
