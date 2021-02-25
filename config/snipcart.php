<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sites
    |--------------------------------------------------------------------------
    |
    | Set the currency, length and weight units for each Statamic site.
    | The units set for Statamic's default site act as default Snipcart units.
    | The units of your other sites will be converted from it.
    |
    | Make sure to keep the sites in sync with your Statamic sites.
    | You can do so by running 'php please snipcart:sync-sites'.
    |
    | Whenever you update a site, you need to run 'php please snipcart:setup'
    | to update your products collection and entries.
    |
    | Currencies: ISO 4217 letter codes supported by Snipcart, eg. USD or EUR
    | Length units: cm, m, in, ft
    | Weight units: g, kg, oz, lb
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
    | Define the handles of the products collection and categories taxonomy.
    |
    | Whenever you change a handle, you need to run 'php please snipcart:setup'
    | to setup the new products collection and categories taxonomy.
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
    'live_secret' => env('SNIPCART_LIVE_SECRET'),

    'test_key' => env('SNIPCART_TEST_KEY'),
    'test_secret' => env('SNIPCART_TEST_SECRET'),

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

    'version' => '3.0.29',

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
    | Snipcart API Cache Lifetime
    |--------------------------------------------------------------------------
    |
    | Define the cache lifetime of Snipcart API responses in seconds.
    | The API is used for things like fetching the stock of a product.
    |
    */

    'api_cache_lifetime' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Snipcart Webhook Route
    |--------------------------------------------------------------------------
    |
    | Define the route where the Snipcart webhook requests will be sent to.
    | Don't forget to add this URL in your Snipcart Dashboard:
    | https://app.snipcart.com/dashboard/webhooks
    |
    | Set this to 'null' to remove the route.
    |
    */

    'webhook' => 'webhooks/snipcart',

];
