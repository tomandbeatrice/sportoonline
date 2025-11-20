<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Shipping Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default shipping provider that will be used
    | when no provider is explicitly specified.
    |
    */
    'default' => env('SHIPPING_DEFAULT_PROVIDER', 'geliver'),

    /*
    |--------------------------------------------------------------------------
    | Shipping Providers
    |--------------------------------------------------------------------------
    |
    | Here you may configure the shipping providers for your application.
    | Each provider has its own API credentials and settings.
    |
    */
    'providers' => [
        'geliver' => [
            'name' => 'Geliver.io',
            'api_key' => env('GELIVER_API_KEY'),
            'base_url' => 'https://api.geliver.io/v1',
            'enabled' => env('GELIVER_ENABLED', true),
            'logo' => '/images/cargo/geliver-logo.png',
        ],

        'aras' => [
            'name' => 'Aras Kargo',
            'username' => env('ARAS_KARGO_USERNAME'),
            'password' => env('ARAS_KARGO_PASSWORD'),
            'customer_code' => env('ARAS_KARGO_CUSTOMER_CODE'),
            'base_url' => 'https://testapi.araskargo.com.tr/api',
            'enabled' => env('ARAS_ENABLED', true),
            'logo' => '/images/cargo/aras-logo.png',
        ],

        'surat' => [
            'name' => 'Sürat Kargo',
            'username' => env('SURAT_KARGO_USERNAME'),
            'password' => env('SURAT_KARGO_PASSWORD'),
            'customer_code' => env('SURAT_KARGO_CUSTOMER_CODE'),
            'base_url' => 'https://api.suratkargo.com.tr/v1',
            'enabled' => env('SURAT_ENABLED', true),
            'logo' => '/images/cargo/surat-logo.png',
        ],

        'ptt' => [
            'name' => 'PTT Kargo',
            'username' => env('PTT_KARGO_USERNAME'),
            'password' => env('PTT_KARGO_PASSWORD'),
            'customer_code' => env('PTT_KARGO_CUSTOMER_CODE'),
            'base_url' => 'https://api.ptt.gov.tr/kargo/v1',
            'enabled' => env('PTT_ENABLED', true),
            'logo' => '/images/cargo/ptt-logo.png',
        ],

        'yurtici' => [
            'name' => 'Yurtiçi Kargo',
            'username' => env('YURTICI_KARGO_USERNAME'),
            'password' => env('YURTICI_KARGO_PASSWORD'),
            'customer_code' => env('YURTICI_KARGO_CUSTOMER_CODE'),
            'base_url' => 'https://api.yurticikargo.com/v3',
            'enabled' => env('YURTICI_ENABLED', true),
            'logo' => '/images/cargo/yurtici-logo.png',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping Settings
    |--------------------------------------------------------------------------
    |
    | General shipping configuration options.
    |
    */
    'free_shipping_threshold' => env('FREE_SHIPPING_THRESHOLD', 500), // 500 TL ve üzeri ücretsiz kargo
    'default_shipping_cost' => env('DEFAULT_SHIPPING_COST', 29.90),
    'weight_based_pricing' => env('WEIGHT_BASED_PRICING', true),
    'auto_create_shipment' => env('AUTO_CREATE_SHIPMENT', false), // Ödeme sonrası otomatik kargo gönderimi
];
