<?php

namespace Aerni\Snipcart\Blueprints;

class ProductBlueprint extends Blueprint
{
    public function __construct()
    {
        parent::__construct('product');
    }

    /**
     * Set the taxonomy on the blueprint.
     *
     * @param string $taxonomy
     * @return self
     */
    public function taxonomy(string $taxonomy): self
    {
        $this->blueprint['sections']['sidebar']['fields'][1]['field']['taxonomies'][0] = $taxonomy;
        return $this;
    }

    public function currency(string $currency): self
    {
        $this->blueprint['sections']['main']['fields'][1]['field']['prepend'] = $currency;
        return $this;
    }
}