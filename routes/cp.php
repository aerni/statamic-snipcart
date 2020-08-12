<?php

use Illuminate\Support\Facades\Route;

Route::namespace('\Aerni\Snipcart\Http\Controllers\CP')
    ->prefix('snipcart/')
    ->group(function () {
        Route::prefix('orverview')->as('overview')->group(function () {
            Route::get('/', 'OverviewController@index')->name('.index');
        });
        Route::prefix('orders')->as('orders')->group(function () {
            Route::get('/', 'OrdersController@index')->name('.index');
        });
    });