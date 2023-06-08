<?php

namespace Aerni\Snipcart\Facades;

use Aerni\Snipcart\Repositories\ProductApiRepository;
use Illuminate\Support\Facades\Facade;

class ProductApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ProductApiRepository::class;
    }
}
