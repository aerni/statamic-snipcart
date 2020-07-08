<?php

use Illuminate\Support\Facades\Route;

Route::namespace('\Aerni\Snipcart\Http\Controllers\Cp')
    ->prefix('snipcart/')
    ->group(function () {
        Route::prefix('products')->as('products')->group(function () {
            Route::get('/', 'ProductController@index')->name('.index');
        });
});