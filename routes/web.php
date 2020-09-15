<?php

use Illuminate\Support\Facades\Route;

$route = config('snipcart.webhook');

if ($route && is_string($route)) {
    Route::snipcart(config('snipcart.webhook'));
}
