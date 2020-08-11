<?php

use Illuminate\Support\Facades\Route;

Route::namespace('\Aerni\Snipcart\Http\Controllers\CP')
    ->prefix('snipcart/')
    ->group(function () {
        Route::prefix('orders')->as('orders')->group(function () {
            Route::get('/', 'OrderController@index')->name('.index');
        });
});