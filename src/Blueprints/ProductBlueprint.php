<?php

namespace Aerni\Snipcart\Blueprints;

class ProductBlueprint extends Blueprint
{
    public function __construct()
    {
        parent::__construct('collections/products/product.yaml', 'product');
    }

    /**
     * Set the category taxonomy on the blueprint.
     *
     * @param string $handle
     * @return self
     */
    public function categories(string $handle): self
    {
        $this->content['sections']['advanced']['fields'][1]['handle'] = $handle;
        $this->content['sections']['advanced']['fields'][1]['field']['taxonomy'] = $handle;
        return $this;
    }

    /**
     * Set the tax taxonomy on the blueprint.
     *
     * @param string $handle
     * @return self
     */
    public function taxes(string $handle): self
    {
        $this->content['sections']['advanced']['fields'][13]['handle'] = $handle;
        $this->content['sections']['advanced']['fields'][13]['field']['taxonomy'] = $handle;
        return $this;
    }
}
