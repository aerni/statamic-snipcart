<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Snipcart API Keys
    |--------------------------------------------------------------------------
    |
    | Your Snipcart API Keys for the Live and Test Environment.
    |
    */

    'live_key' => env('SNIPCART_LIVE_KEY'),
    'test_key' => env('SNIPCART_TEST_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    |
    | Set this to "false" to use the "live_key" and process real transactions.
    |
    */

    'test_mode' => env('SNIPCART_TEST_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Snipcart Version
    |--------------------------------------------------------------------------
    |
    | The Snipcart version you want to use.
    |
    */

    'version' => env('SNIPCART_VERSION', '3.0.17'),

    /*
    |--------------------------------------------------------------------------
    | Cart Summary Behaviour
    |--------------------------------------------------------------------------
    |
    | Setting this to "none" prevents the cart from opening every time
    | a product is added.
    |
    */

    'behaviour' => null,

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The currency you want to use.
    |
    */

    'currency' => 'CHF',

    /*
    |--------------------------------------------------------------------------
    | Length Unit
    |--------------------------------------------------------------------------
    |
    | Choose between the following length units: cm, m, in, ft.
    |
    */
    
    'length' => 'cm',

    /*
    |--------------------------------------------------------------------------
    | Weight Unit
    |--------------------------------------------------------------------------
    |
    | Choose between the following weight units: g, kg, oz, lb.
    |
    */
    
    'weight' => 'kg',
    
    /*
    |--------------------------------------------------------------------------
    | Image Manipulation Settings
    |--------------------------------------------------------------------------
    |
    | Define a Glide preset to be applied on the product image that shows 
    | in the cart. You may also turn the manipulation off (not recommended).
    |
    */

    'image' => [
        'manipulation' => true,
        'preset' => ['w' => 240, 'q' => 75],
    ]

];