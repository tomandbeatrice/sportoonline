<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Iyzico API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your credentials for the Iyzico API.
    | These will be read from your .env file for security.
    |
    */

    'api_key' => env('IYZICO_API_KEY'),
    'secret_key' => env('IYZICO_SECRET_KEY'),
    'base_url' => env('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'), // Default to sandbox

    /*
    |--------------------------------------------------------------------------
    | Marketplace Sub-merchant Keys
    |--------------------------------------------------------------------------
    |
    | For marketplace payments, you need to specify the sub-merchant keys.
    | This is a placeholder and will be dynamically retrieved per seller.
    |
    */
    'sub_merchant_key' => env('IYZICO_SUB_MERCHANT_KEY'),

];
