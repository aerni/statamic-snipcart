<?php

namespace Aerni\Snipcart\Facades;

use Aerni\Snipcart\Repositories\CurrencyRepository;
use Illuminate\Support\Facades\Facade;

class Currency extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CurrencyRepository::class;
    }
}
