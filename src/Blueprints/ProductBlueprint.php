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
        $this->blueprint['sections']['product']['fields'][6]['field']['taxonomies'][0] = $taxonomy;

        return $this;
    }
}
