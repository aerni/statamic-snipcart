<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Blueprints\Blueprint;
use Statamic\Facades\Blueprint as StatamicBlueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;
use Statamic\Support\Str;

class Content
{
    protected $products;
    protected $categories;
    protected $taxes;
    
    public function __construct()
    {
        $this->products = config('snipcart.collections.products');
        $this->categories = config('snipcart.taxonomies.categories');
        $this->taxes = config('snipcart.taxonomies.taxes');
    }

    /**
     * Setup the content.
     *
     * @return void
     */
    public function setup(): void
    {
        $this->setupCollection();
        $this->setupTaxonomies();
    }

    /**
     * Setup the product collection and its blueprint.
     *
     * @return void
     */
    protected function setupCollection(): void
    {
        if (! Collection::handleExists($this->products)) {
            Collection::make($this->products)
                ->title(Str::studlyToTitle($this->products))
                ->pastDateBehavior('public')
                ->futureDateBehavior('private')
                ->routes('/' . Str::slug(Str::studlyToTitle($this->products)) . '/{slug}')
                ->taxonomies([$this->categories])
                ->save();
        }

        if (! StatamicBlueprint::find("collections/{$this->products}/product")) {
            (new Blueprint())
                ->parse("collections/products/product.yaml")
                ->make('product')
                ->namespace("collections.{$this->products}")
                ->save();
        }

        $this->updateProductBlueprint();
    }

    /**
     * Setup the product taxonomies and their blueprints.
     *
     * @return void
     */
    protected function setupTaxonomies(): void
    {
        if (! Taxonomy::handleExists($this->categories)) {
            Taxonomy::make($this->categories)
                ->title(Str::studlyToTitle($this->categories))
                ->save();
        }

        if (! StatamicBlueprint::find("taxonomies/{$this->categories}/category")) {
            (new Blueprint())
                ->parse("taxonomies/categories/category.yaml")
                ->make('category')
                ->namespace("taxonomies.{$this->categories}")
                ->save();
        }

        if (! Taxonomy::handleExists($this->taxes)) {
            Taxonomy::make($this->taxes)
                ->title(Str::studlyToTitle($this->taxes))
                ->save();
        }

        if (! StatamicBlueprint::find("taxonomies/{$this->taxes}/tax")) {
            (new Blueprint())
                ->parse("taxonomies/taxes/tax.yaml")
                ->make('tax')
                ->namespace("taxonomies.{$this->taxes}")
                ->save();
        }

    }

    /**
     * Update the product blueprint with the categories and taxes taxonomies.
     *
     * @return void
     */
    protected function updateProductBlueprint(): void
    {
        $blueprint = StatamicBlueprint::find("collections/{$this->products}/product");

        $content = $blueprint->contents();

        $content['sections']['advanced']['fields'][1]['handle'] = $this->categories;
        $content['sections']['advanced']['fields'][1]['field']['taxonomy'] = $this->categories;
        $content['sections']['advanced']['fields'][13]['handle'] = $this->taxes;
        $content['sections']['advanced']['fields'][13]['field']['taxonomy'] = $this->taxes;

        $blueprint->setContents($content)->save();
    }
}
