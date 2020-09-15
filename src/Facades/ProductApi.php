<?php

namespace Aerni\Snipcart\Facades;

use Illuminate\Support\Facades\Facade;

class ProductApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ProductApi';
    }
}
