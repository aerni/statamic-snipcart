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

    'version' => env('SNIPCART_VERSION', '3.0.17'),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency you want to use.
    |
    */

    'default_currency' => 'USD',
    
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