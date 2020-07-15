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
        $this->blueprint['sections']['product']['fields'][7]['field']['taxonomies'][0] = $taxonomy;

        return $this;
    }

    public function currency(string $currency): self
    {
        $this->blueprint['sections']['product']['fields'][4]['field']['prepend'] = $currency;
        $this->blueprint['sections']['custom_fields']['fields'][0]['field']['sets']['dropdown']['fields'][1]['field']['fields'][1]['field']['prepend'] = $currency;

        return $this;
    }
}
