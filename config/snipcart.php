<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Collections & Taxonomies
    |--------------------------------------------------------------------------
    |
    | Define the handles of the products collection and taxonomies.
    | Changing a value will automatically create the relevant collection/taxonomy
    | and update the taxonomies in the product blueprint.
    |
    */

    'collections' => [
        'products' => 'products',
    ],
    
    'taxonomies' => [
        'categories' => 'categories',
        'taxes' => 'taxes',
    ],

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
    | Set this to "false" to start processing real transactions.
    | You probably want to do this in production only.
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

    'version' => '3.0.17',

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Define the currency you want to use.
    |
    */

    'currency' => 'USD',

    /*
    |--------------------------------------------------------------------------
    | Length Unit
    |--------------------------------------------------------------------------
    |
    | Define the lenght unit you want to use. 
    | You can choose between the following options: 'cm', 'm', 'in', 'ft'.
    |
    */
    
    'length' => 'cm',

    /*
    |--------------------------------------------------------------------------
    | Weight Unit
    |--------------------------------------------------------------------------
    |
    | Define the weight unit you want to use. 
    | You can choose between the following options: 'g', 'kg', 'oz', 'lb'.
    |
    */
    
    'weight' => 'g',

    /*
    |--------------------------------------------------------------------------
    | Cart Summary Behaviour
    |--------------------------------------------------------------------------
    |
    | Setting this to "none" prevents the cart from opening every time
    | a product is added. Default is "null".
    |
    */

    'behaviour' => null,
    
    /*
    |--------------------------------------------------------------------------
    | Cart Image Manipulation Settings
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