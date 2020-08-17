<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class SitesNotInSyncException extends Exception implements ProvidesSolution
{
    protected $siteHandle;

    public function __construct(string $siteHandle)
    {
        parent::__construct("Your sites are not in sync.");

        $this->siteHandle = $siteHandle;
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("The sites array of the Statamic and Snipcart config need to be in sync.")
            ->setSolutionDescription("Add a new site with the handle `{$this->siteHandle}` to `config/snipcart.php`")
            ->setDocumentationLinks([
                'Read the config guide' => 'https://snipcart.docs.michaelaerni.ch/setup/configuration',
            ]);
    }
}
