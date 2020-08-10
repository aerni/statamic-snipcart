<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class ApiKeyNotFoundException extends Exception implements ProvidesSolution
{
    protected $mode;

    public function __construct($mode)
    {
        parent::__construct("Could not find a Snipcart API Key.");

        $this->mode = $mode;
    }

    public function getSolution(): Solution
    {
        $description = $this->mode
            ? "Add your Snipcart API Key to `SNIPCART_TEST_KEY` in your `.env`"
            : "Add your Snipcart API Key to `SNIPCART_LIVE_KEY` in your `.env`";

        return BaseSolution::create("You didn't set a Snipcart API Key.")
            ->setSolutionDescription($description)
            ->setDocumentationLinks([   
                'Read the API Key guide' => 'https://snipcart.docs.michaelaerni.ch/setup/installation#add-snipcart-api-keys',
            ]);
    }
}
