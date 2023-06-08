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
     * Execute the console command.
     */
    public function handle(): void
    {
        collect(config('snipcart.products'))->each(function ($config) {
            $this->setupCollection($config);
            $this->setupTaxonomies($config);
        });

        $this->info('Snipcart is configured and ready to go!');
    }

    /**
     * Setup the product collection and its blueprint.
     */
    protected function setupCollection(array $config): void
    {
        $collection = $config['collection'];

        if (! Collection::handleExists($collection) || $this->option('force')) {
            Collection::make($collection)
                ->title(Str::studlyToTitle($collection))
                ->taxonomies($config['taxonomies'])
                ->save();

            $this->line("<info>[✓]</info> Created collection at <comment>content/collections/{$collection}.yaml</comment>");
        }

        $blueprint = Str::singular($collection);

        if (! StatamicBlueprint::find("collections/{$collection}/{$blueprint}") || $this->option('force')) {
            (new Blueprint())
                ->parse('collections/products/product.yaml')
                ->make($blueprint)
                ->namespace("collections.{$collection}")
                ->save();

            $this->line("<info>[✓]</info> Created blueprint at <comment>resources/blueprints/collections/{$collection}/{$blueprint}.yaml</comment>");
        }
    }

    /**
     * Setup the product taxonomies.
     */
    protected function setupTaxonomies(array $config): void
    {
        collect($config['taxonomies'])
            ->each(fn ($taxonomy) => $this->setupTaxonomy($taxonomy));
    }

    /**
     * Setup the product taxonomy and its blueprint.
     */
    protected function setupTaxonomy(string $handle): void
    {
        if (! Taxonomy::handleExists($handle) || $this->option('force')) {
            Taxonomy::make($handle)
                ->title(Str::studlyToTitle($handle))
                ->save();

            $this->line("<info>[✓]</info> Created taxonomy at <comment>content/taxonomies/{$handle}.yaml</comment>");
        }

        $blueprint = Str::singular($handle);

        if (! StatamicBlueprint::find("taxonomies/{$handle}/{$blueprint}") || $this->option('force')) {
            (new Blueprint())
                ->parse('taxonomies/categories/category.yaml')
                ->make($blueprint)
                ->namespace("taxonomies.{$handle}")
                ->save();

            $this->line("<info>[✓]</info> Created blueprint at <comment>resources/blueprints/taxonomies/{$handle}/{$blueprint}.yaml</comment>");
        }
    }
}
