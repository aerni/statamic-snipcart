<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class UnsupportedAttributeException extends Exception implements ProvidesSolution
{
    public function __construct()
    {
        parent::__construct("You are missing a required product attribute.");
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Please make sure to set all the required product attributes.")
            ->setSolutionDescription("Required attributes: `name`, `id`, `price`, `url`");
    }
}
