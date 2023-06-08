<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class ApiKeyNotFoundException extends Exception implements ProvidesSolution
{
    public function __construct(protected bool $inTestMode)
    {
        parent::__construct('Could not find a Snipcart API Key.');
    }

    public function getSolution(): Solution
    {
        $description = $this->inTestMode
            ? 'Add your Snipcart API Key to `SNIPCART_TEST_KEY` in your `.env`'
            : 'Add your Snipcart API Key to `SNIPCART_LIVE_KEY` in your `.env`';

        return BaseSolution::create("You didn't set a Snipcart API Key.")
            ->setSolutionDescription($description)
            ->setDocumentationLinks([
                'Read the API Key guide' => 'https://snipcart.docs.michaelaerni.ch/setup/installation#add-snipcart-api-keys',
            ]);
    }
}
