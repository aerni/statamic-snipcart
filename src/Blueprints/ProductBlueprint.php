<?php

namespace Aerni\Snipcart\Blueprints;

use Illuminate\Support\Str;
use Statamic\Facades\Blueprint as StatamicBlueprint;

class ProductBlueprint extends Blueprint
{
    public function __construct()
    {
        parent::__construct('product');
    }

    /**
     * Make a blueprint with the given $title.
     *
     * @param string $title
     * @param string $handle
     * @return void
     */
    public function make(string $title, string $handle): void
    {
        $this->title($title);
        StatamicBlueprint::make(Str::snake($title))->setNamespace("collections.{$handle}")->setContents($this->blueprint)->save();
    }

    /**
     * Set the taxonomy on the blueprint.
     *
     * @param string $taxonomy
     * @return self
     */
    public function taxonomy(string $taxonomy): self
    {
        $this->blueprint['sections']['advanced']['fields'][1]['field']['taxonomies'][0] = $taxonomy;

        return $this;
    }
}
