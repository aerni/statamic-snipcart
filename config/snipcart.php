<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Collections & Taxonomies
    |--------------------------------------------------------------------------
    |
    | Define the handles of the products collection and taxonomies.
    |
    | If you change a value, you need to run 'php please snipcart:setup' 
    | to re-generate the collection, taxonomies, and blueprints.
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
    'live_secret' => env('SNIPCART_LIVE_SECRET'),
    
    'test_key' => env('SNIPCART_TEST_KEY'),
    'test_secret' => env('SNIPCART_TEST_SECRET'),

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

    'version' => '3.0.19',

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
    | Define the length unit you want to use. 
    | You can choose between the following options: 'cm', 'm', 'in', 'ft'.
    |
    | If you change a value, you may run 'php please snipcart:migrate'
    | to convert your products' lengths to the new unit.
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
    | If you change a value, you may run 'php please snipcart:migrate'
    | to convert your products' lengths to the new unit.
    |
    */
    
    'weight' => 'g',

    /*
    |--------------------------------------------------------------------------
    | Cart Behaviour
    |--------------------------------------------------------------------------
    |
    | Set this to "none" to prevent the cart from opening every time
    | a product is added. Default is "null".
    |
    */

    'behaviour' => null,
    
    /*
    |--------------------------------------------------------------------------
    | Cart Image
    |--------------------------------------------------------------------------
    |
    | Define a Glide preset to be applied to the product image that shows 
    | in the cart. You may also turn the manipulation off (not recommended).
    |
    */

    'image' => [
        'manipulation' => true,
        'preset' => ['w' => 240, 'q' => 75],
    ]

];