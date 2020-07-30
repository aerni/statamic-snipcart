<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Blueprints\Blueprint;
use Statamic\Facades\Blueprint as StatamicBlueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;
use Statamic\Support\Str;

class Content
{
    /**
     * If this is true, existing content will be overridden.
     *
     * @var bool
     */
    protected $force = false;

    /**
     * The products collection handle
     *
     * @var string
     */
    protected $products;

    /**
     * The categories taxonomy handle.
     *
     * @var string
     */
    protected $categories;

    /**
     * The taxes taxonomy handle.
     *
     * @var string
     */
    protected $taxes;

    /**
     * Contains messages when something was setup.
     *
     * @var array
     */
    protected $messages = [];
    
    public function __construct()
    {
        $this->products = config('snipcart.collections.products');
        $this->categories = config('snipcart.taxonomies.categories');
        $this->taxes = config('snipcart.taxonomies.taxes');
    }

    /**
     * Setup the content.
     *
     * @param bool $force
     * @return void
     */
    public function setup(bool $force = false): void
    {
        $this->force = $force;

        $this->setupCollection();
        $this->setupTaxonomies();
        $this->updateProductBlueprint();
    }

    /**
     * Setup the product collection and its blueprint.
     *
     * @return void
     */
    protected function setupCollection(): void
    {
        if (! Collection::handleExists($this->products) || $this->force) {
            Collection::make($this->products)
                ->title(Str::studlyToTitle($this->products))
                ->template('product')
                ->layout('layout')
                ->sortDirection('asc')
                ->pastDateBehavior('public')
                ->futureDateBehavior('private')
                ->routes('/' . Str::slug(Str::studlyToTitle($this->products)) . '/{slug}')
                ->taxonomies([$this->categories])
                ->save();
            
            array_push($this->messages, "Created Collection: <comment>{$this->products}</comment>");
        }

        if (! StatamicBlueprint::find("collections/{$this->products}/product") || $this->force) {
            (new Blueprint())
                ->parse("collections/products/product.yaml")
                ->make('product')
                ->namespace("collections.{$this->products}")
                ->save();

            array_push($this->messages, "Created Blueprint: <comment>collections/{$this->products}/product</comment>");
        }
    }

    /**
     * Setup the product taxonomies and their blueprints.
     *
     * @return void
     */
    protected function setupTaxonomies(): void
    {
        if (! Taxonomy::handleExists($this->categories) || $this->force) {
            Taxonomy::make($this->categories)
                ->title(Str::studlyToTitle($this->categories))
                ->save();

            array_push($this->messages, "Created Taxonomy: <comment>{$this->categories}</comment>");
        }

        if (! StatamicBlueprint::find("taxonomies/{$this->categories}/category") || $this->force) {
            (new Blueprint())
                ->parse("taxonomies/categories/category.yaml")
                ->make('category')
                ->namespace("taxonomies.{$this->categories}")
                ->save();

            array_push($this->messages, "Created Blueprint: <comment>taxonomies/{$this->categories}/category</comment>");
        }

        if (! Taxonomy::handleExists($this->taxes) || $this->force) {
            Taxonomy::make($this->taxes)
                ->title(Str::studlyToTitle($this->taxes))
                ->save();

            array_push($this->messages, "Created Taxnomoy: <comment>{$this->taxes}</comment>");
        }

        if (! StatamicBlueprint::find("taxonomies/{$this->taxes}/tax") || $this->force) {
            (new Blueprint())
                ->parse("taxonomies/taxes/tax.yaml")
                ->make('tax')
                ->namespace("taxonomies.{$this->taxes}")
                ->save();

            array_push($this->messages, "Created Blueprint: <comment>taxonomies/{$this->taxes}/tax</comment>");
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

        array_push($this->messages, "Updated Blueprint: <comment>collections/{$this->products}/product</comment>");
    }

    /**
     * Get the messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return $this->messages;
    }
}
