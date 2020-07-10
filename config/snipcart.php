<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Snipcart API Key
    |--------------------------------------------------------------------------
    |
    | Your Snipcart API Key.
    |
    */

    'key' => env('SNIPCART_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Snipcart Version
    |--------------------------------------------------------------------------
    |
    | The Snipcart version you want to use.
    |
    */

    'version' => env('SNIPCART_VERSION', '3.0.16'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The currency you want to use.
    |
    */

    'currency' => env('SNIPCART_CURRENCY', 'USD'),
    
    /*
    |--------------------------------------------------------------------------
    | Cart Summary Behaviour
    |--------------------------------------------------------------------------
    |
    | Setting this to "none" prevents the cart from opening every time
    | a product is added.
    |
    */

    'behaviour' => env('SNIPCART_BEHAVIOUR', null),

];