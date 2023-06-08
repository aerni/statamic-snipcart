<?php

use Illuminate\Support\Facades\Route;

if ($route = config('snipcart.webhook')) {
    Route::snipcart($route);
}
