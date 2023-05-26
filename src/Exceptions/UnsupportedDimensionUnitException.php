<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class UnsupportedDimensionUnitException extends Exception implements ProvidesSolution
{
    public function __construct(protected string $siteHandle, protected string $dimension, protected string $unit)
    {
        parent::__construct("The unit [{$unit}] is not supported.");
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Provide a valid {$this->dimension} unit in the config.")
            ->setSolutionDescription("Set the value of `{$this->dimension}` of the `{$this->siteHandle}` site to a supported {$this->dimension} unit.")
            ->setDocumentationLinks([
                'Read the config guide' => 'https://snipcart.docs.michaelaerni.ch/setup/configuration',
            ]);
    }
}
