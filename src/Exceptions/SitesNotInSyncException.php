<?php

namespace Aerni\Snipcart\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class SitesNotInSyncException extends Exception implements ProvidesSolution
{
    public function __construct(protected string $siteHandle)
    {
        parent::__construct('Your sites are not in sync.');
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('The sites array of the Statamic and Snipcart config need to be in sync.')
            ->setSolutionDescription("Add a new site with the handle `{$this->siteHandle}` to `config/snipcart.php`")
            ->setDocumentationLinks([
                'Read the config guide' => 'https://snipcart.docs.michaelaerni.ch/setup/configuration',
            ]);
    }
}
