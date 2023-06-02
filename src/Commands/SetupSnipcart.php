<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Blueprints\Blueprint;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Blueprint as StatamicBlueprint;
use Statamic\Facades\Collection;
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
        $this->products = config('snipcart.products.collection');
        $this->categories = config('snipcart.categories.taxonomy');
        $this->force = $this->option('force');

        $this->setupCollection();
        $this->setupTaxonomies();

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
}
