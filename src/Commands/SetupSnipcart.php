<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Blueprints\Blueprint;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Blueprint as StatamicBlueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;
use Statamic\Facades\Taxonomy;
use Statamic\Support\Str;

class SetupSnipcart extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipcart:setup {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Snipcart';

    /**
     * Override existing collections, taxaonomies and blueprints.
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
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->products = config('snipcart.collections.products');
        $this->categories = config('snipcart.taxonomies.categories');
        $this->taxes = config('snipcart.taxonomies.taxes');
        $this->force = $this->option('force');

        $this->setupCollection();
        $this->setupTaxonomies();
        $this->update();

        $this->info("Snipcart is configured and ready to go!");
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
                ->sites($this->sites())
                ->template($this->products . '/show')
                ->layout('layout')
                ->sortDirection('asc')
                ->pastDateBehavior('public')
                ->futureDateBehavior('private')
                ->routes('/' . Str::slug(Str::studlyToTitle($this->products)) . '/{slug}')
                ->taxonomies([$this->categories])
                ->save();

            $this->info("Created Collection: <comment>{$this->products}</comment>");
        }

        if (! StatamicBlueprint::find("collections/{$this->products}/product") || $this->force) {
            (new Blueprint())
                ->parse("collections/products/product.yaml")
                ->make('product')
                ->namespace("collections.{$this->products}")
                ->save();

            $this->info("Created Blueprint: <comment>collections/{$this->products}/product</comment>");
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

            $this->info("Created Taxonomy: <comment>{$this->categories}</comment>");
        }

        if (! StatamicBlueprint::find("taxonomies/{$this->categories}/category") || $this->force) {
            (new Blueprint())
                ->parse("taxonomies/categories/category.yaml")
                ->make('category')
                ->namespace("taxonomies.{$this->categories}")
                ->save();

            $this->info("Created Blueprint: <comment>taxonomies/{$this->categories}/category</comment>");
        }

        if (! Taxonomy::handleExists($this->taxes) || $this->force) {
            Taxonomy::make($this->taxes)
                ->title(Str::studlyToTitle($this->taxes))
                ->save();

            $this->info("Created Taxnomoy: <comment>{$this->taxes}</comment>");
        }

        if (! StatamicBlueprint::find("taxonomies/{$this->taxes}/tax") || $this->force) {
            (new Blueprint())
                ->parse("taxonomies/taxes/tax.yaml")
                ->make('tax')
                ->namespace("taxonomies.{$this->taxes}")
                ->save();

            $this->info("Created Blueprint: <comment>taxonomies/{$this->taxes}/tax</comment>");
        }
    }

    /**
     * Update the products collection and its blueprint.
     *
     * @return void
     */
    protected function update(): void
    {
        $this->updateProductsTaxonomies();
        $this->updateProductBlueprint();
    }

    /**
     * Update the products collection taxonomies.
     *
     * @return void
     */
    protected function updateProductsTaxonomies(): void
    {
        $productsCollection = Collection::find($this->products);

        $taxonomies = $productsCollection->taxonomies()
            ->transform(function ($item) {
                return $item->handle();
            })
            ->merge([$this->categories])
            ->unique()
            ->toArray();

        $productsCollection->taxonomies($taxonomies)
            ->save();

        $this->info("Updated taxonomies in <comment>{$this->products}</comment> collection");
    }

    /**
     * Update the product blueprint with the new categories and taxes taxonomies.
     *
     * @return void
     */
    protected function updateProductBlueprint(): void
    {
        $productBlueprint = StatamicBlueprint::find("collections/{$this->products}/product");

        $content = $productBlueprint->contents();

        $content['sections']['basic']['fields'][5]['handle'] = $this->categories;
        $content['sections']['basic']['fields'][5]['field']['taxonomy'] = $this->categories;

        $content['sections']['advanced']['fields'][10]['handle'] = $this->taxes;
        $content['sections']['advanced']['fields'][10]['field']['taxonomy'] = $this->taxes;

        $productBlueprint->setContents($content)->save();

        $this->info("Updated taxonomies in <comment>collections/{$this->products}/product</comment> blueprint");
    }

    /**
     * Get all the site handles from config/statamic/sites.php
     *
     * @return array
     */
    protected function sites(): array
    {
        return Site::all()->map(function ($item) {
            return $item->handle();
        })->flatten()->toArray();
    }
}
