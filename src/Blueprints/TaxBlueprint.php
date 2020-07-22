<?php

namespace Aerni\Snipcart\Blueprints;

class TaxBlueprint extends Blueprint
{
    public function __construct()
    {
        parent::__construct('taxonomies/taxes/tax.yaml', 'tax');
    }
}
