<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class UnsupportedDimensionUnitException extends Exception implements ProvidesSolution
{
    protected $type;
    protected $unit;

    public function __construct(string $type, string $unit)
    {
        parent::__construct("The unit [{$unit}] is not supported.");

        $this->type = $type;
        $this->unit = $unit;
    }

    public function getSolution(): Solution
    {
        if ($this->type === 'length') {
            $description = "Change the value of the `length` key to a supported length unit.";
        }

        if ($this->type === 'weight') {
            $description = "Change the value of the `weight` key to a supported weight unit.";
        }

        return BaseSolution::create("Please set a valid {$this->type} unit in the config.")
            ->setSolutionDescription($description)
            ->setDocumentationLinks([
                'Read the config guide' => 'https://snipcart.docs.michaelaerni.ch/setup/configuration',
            ]);
    }
}
