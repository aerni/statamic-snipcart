<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sites
    |--------------------------------------------------------------------------
    |
    | The sites need to be in sync with your Statamic config. Make sure to
    | add a new key for each site set in `config/statamic/sites.php`.
    |
    | Snipcart's default currency, length and weight unit will be the ones
    | defined within the key of Statamic's default site.
    |
    | If you add or remove a site or change a value, you need to run
    | 'php please snipcart:migrate' to update the products collection and entries.
    |
    | Accepted length units: cm, m, in, ft
    | Accepted weight units: g, kg, oz, lb
    |
    */

    'sites' => [

        'default' => [
            'currency' => 'USD',
            'length' => 'in',
            'weight' => 'oz',
        ],

    ],

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
    | Set this to 'false' to start processing real transactions.
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
    | Cart Behaviour
    |--------------------------------------------------------------------------
    |
    | Set this to 'none' to prevent the cart from opening every time
    | a product is added. Default is 'null'.
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
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Route
    |--------------------------------------------------------------------------
    |
    | Define the route where the Snipcart webhook requests will be sent to.
    | Don't forget to add this URL in your Snipcart Dashboard:
    | https://app.snipcart.com/dashboard/webhooks
    |
    */

    'webhook' => 'snipcart-webhook',

];
