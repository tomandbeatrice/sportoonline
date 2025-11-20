<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Marketplace Settings
    |--------------------------------------------------------------------------
    |
    | This file contains settings specific to the C2C marketplace functionality.
    |
    */

    /**
     * The commission rate the platform takes from each sale.
     * This is a decimal value. For example, 0.15 means 15%.
     */
    'commission_rate' => env('MARKETPLACE_COMMISSION_RATE', 0.15),

    /**
     * The currency used for all transactions.
     * Make sure this is supported by your payment provider.
     */
    'currency' => 'TRY',

];
