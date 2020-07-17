<?php

namespace Aerni\Snipcart\Facades;

use Illuminate\Support\Facades\Facade;

class Currency extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Currency';
    }
}