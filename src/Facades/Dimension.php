<?php

namespace Aerni\Snipcart\Facades;

use Aerni\Snipcart\Repositories\DimensionRepository;
use Illuminate\Support\Facades\Facade;

class Dimension extends Facade
{
    protected static function getFacadeAccessor()
    {
        return DimensionRepository::class;
    }
}
