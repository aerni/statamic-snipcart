<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sites
    |--------------------------------------------------------------------------
    |
    | Set the currency, length and weight units for each Statamic site.
    | In a multi-site setup, the units will be converted from a product's root entry.
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
    | Configure your product collections and taxonomies.
    |
    */

    'products' => [
        [
            'collection' => 'products',
            'taxonomies' => ['categories'],
        ],
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
    | Snipcart Settings
    |--------------------------------------------------------------------------
    |
    | Configure any settings that you want to apply to the Snipcart script.
    | Make sure to set the keys exactly as documented, e.g. 'LoadCSS'.
    | Available settings: https://docs.snipcart.com/v3/setup/installation#settings
    |
    */

    'snipcart_settings' => [],

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
    */

    'webhook' => 'webhooks/snipcart',

];
