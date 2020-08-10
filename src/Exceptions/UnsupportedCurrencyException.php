<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class UnsupportedCurrencyException extends Exception implements ProvidesSolution
{
    protected $currency;

    public function __construct(string $currency)
    {
        parent::__construct("The currency [{$currency}] is not supported.");

        $this->unit = $currency;
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Please set a valid currency code in the config.")
            ->setSolutionDescription("Change the value of the `currency` key to a supported currency.")
            ->setDocumentationLinks([
                'Read the config guide' => 'https://snipcart.docs.michaelaerni.ch/setup/configuration',
            ]);
    }
}
