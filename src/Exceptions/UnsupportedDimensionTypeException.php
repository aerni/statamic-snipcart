<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;

class UnsupportedDimensionTypeException extends Exception
{
    public function __construct()
    {
        parent::__construct("Please set the dimension type to either [length] or [weight].");
    }
}
