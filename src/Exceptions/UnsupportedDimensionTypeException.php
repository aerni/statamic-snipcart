<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class UnsupportedDimensionTypeException extends Exception implements ProvidesSolution
{
    public function __construct(string $dimension)
    {
        parent::__construct("The dimension [{$dimension}] is not supported.");
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Provide a valid option to the dimension fieldtype.")
            ->setSolutionDescription("Set the value to either `length` or `weight`.")
            ->setDocumentationLinks([
                'Read the config guide' => 'https://snipcart.docs.michaelaerni.ch/setup/configuration',
            ]);
    }
}
