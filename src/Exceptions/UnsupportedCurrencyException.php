<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class UnsupportedCurrencyException extends Exception implements ProvidesSolution
{
    protected $siteHandle;

    public function __construct(string $siteHandle, string $currency)
    {
        parent::__construct("The currency [{$currency}] is not supported.");

        $this->siteHandle = $siteHandle;
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Provide a valid currency code in the config.")
            ->setSolutionDescription("Set the value of `currency` of the `{$this->siteHandle}` site to a supported currency code.")
            ->setDocumentationLinks([
                'Read the config guide' => 'https://snipcart.docs.michaelaerni.ch/setup/configuration',
            ]);
    }
}
