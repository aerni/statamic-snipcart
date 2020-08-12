<?php

namespace Aerni\Snipcart\Facades;

use Illuminate\Support\Facades\Facade;

class Orders extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Orders';
    }
}
