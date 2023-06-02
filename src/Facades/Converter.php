<?php

namespace Aerni\Snipcart\Facades;

use Aerni\Snipcart\Support\Converter as SupportConverter;
use Illuminate\Support\Facades\Facade;

class Converter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SupportConverter::class;
    }
}
