<?php

namespace Aerni\Snipcart\Facades;

use Illuminate\Support\Facades\Facade;

class Product extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Product';
    }
}
