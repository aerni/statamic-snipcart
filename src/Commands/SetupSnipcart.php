<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Blueprints\Blueprint;
use Aerni\Snipcart\Facades\Converter;
use Aerni\Snipcart\Facades\Dimension;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Blueprint as StatamicBlueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Facades\Taxonomy;
use Statamic\Support\Str;

class SetupSnipcart extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'snipcart:setup {--force}';

    /**
     * The console command description.
     */
    protected $description = 'Setup Snipcart';

    /**
     * Override existing collections, taxaonomies and blueprints.
     */
    protected bool $force = false;

    /**
     * The products collection handle
     */
    protected string $products;

    /**
     * The categories taxonomy handle.
     */
    protected string $categories;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->products = config('snipcart.collections.products');
        $this->categories = config('snipcart.taxonomies.categories');
        $this->force = $this->option('force');

        $this->setupCollection();
        $this->setupTaxonomies();
        $this->update();

        $this->info('Snipcart is configured and ready to go!');
    }

    /**
     * Setup the product collection and its blueprint.
     */
    protected function setupCollection(): void
    {
        if (! Collection::handleExists($this->products) || $this->force) {
            Collection::make($this->products)
                ->title(Str::studlyToTitle($this->products))
                ->sites($this->sites())
                ->template($this->products.'/show')
                ->layout('layout')
                ->sortDirection('asc')
                ->pastDateBehavior('public')
                ->futureDateBehavior('private')
                ->routes('/'.Str::slug(Str::studlyToTitle($this->products)).'/{slug}')
                ->taxonomies([$this->categories])
                ->save();

            $this->line("<info>[✓]</info> Created collection at <comment>content/collections/{$this->products}.yaml</comment>");
        }

        if (! StatamicBlueprint::find("collections/{$this->products}/product") || $this->force) {
            (new Blueprint())
                ->parse('collections/products/product.yaml')
                ->make('product')
                ->namespace("collections.{$this->products}")
                ->save();

            $this->line("<info>[✓]</info> Created blueprint at <comment>resources/blueprints/collections/{$this->products}/product.yaml</comment>");
        }
    }

    /**
     * Setup the product taxonomies and their blueprints.
     */
    protected function setupTaxonomies(): void
    {
        if (! Taxonomy::handleExists($this->categories) || $this->force) {
            Taxonomy::make($this->categories)
                ->title(Str::studlyToTitle($this->categories))
                ->save();

            $this->line("<info>[✓]</info> Created taxonomy at <comment>content/taxonomies/{$this->categories}.yaml</comment>");
        }

        if (! StatamicBlueprint::find("taxonomies/{$this->categories}/category") || $this->force) {
            (new Blueprint())
                ->parse('taxonomies/categories/category.yaml')
                ->make('category')
                ->namespace("taxonomies.{$this->categories}")
                ->save();

            $this->line("<info>[✓]</info> Created blueprint at <comment>resources/blueprints/taxonomies/{$this->categories}/category.yaml</comment>");
        }
    }

    /**
     * Update the products collection and its blueprint.
     */
    protected function update(): void
    {
        $this->updateProductsCollection();
        $this->updateProductBlueprint();
        $this->convertUnits();
    }

    /**
     * Update the products collection taxonomies.
     */
    protected function updateProductsCollection(): void
    {
        $productsCollection = Collection::find($this->products);

        $taxonomies = $productsCollection->taxonomies()
            ->transform(function ($item) {
                return $item->handle();
            })
            ->merge([$this->categories])
            ->unique()
            ->toArray();

        $productsCollection
            ->taxonomies($taxonomies)
            ->sites($this->sites())
            ->save();

        $this->line("<info>[✓]</info> Updated sites in <comment>content/collections/{$this->products}.yaml</comment>");
        $this->line("<info>[✓]</info> Updated taxonomies in <comment>content/collections/{$this->products}.yaml</comment>");
    }

    /**
     * Update the product blueprint with the new categories taxonomies.
     */
    protected function updateProductBlueprint(): void
    {
        $productBlueprint = StatamicBlueprint::find("collections/{$this->products}/product");

        $content = $productBlueprint->contents();

        $content['tabs']['sidebar']['sections'][0]['fields'][2]['handle'] = $this->categories;
        $content['tabs']['sidebar']['sections'][0]['fields'][2]['field']['taxonomies'] = $this->categories;

        $productBlueprint->setContents($content)->save();

        $this->line("<info>[✓]</info> Updated taxonomies in <comment>resources/blueprints/collections/{$this->products}/product.yaml</comment>");
    }

    /**
     * Convert the length/weight units in the product's root entry.
     */
    protected function convertUnits(): void
    {
        $lengthUnit = Dimension::from(Site::default())->type('length')->short();
        $weightUnit = Dimension::from(Site::default())->type('weight')->short();

        Entry::whereCollection($this->products)->each(function ($entry) {
            Converter::convertEntryDimensions($entry);
        });

        $this->line("<info>[✓]</info> Converted length to <comment>{$lengthUnit}</comment>");
        $this->line("<info>[✓]</info> Converted weight to <comment>{$weightUnit}</comment>");
    }

    /**
     * Get all the site handles from config/statamic/sites.php
     */
    protected function sites(): array
    {
        return Site::all()
            ->map(fn ($site) => $site->handle())
            ->flatten()
            ->toArray();
    }
}
