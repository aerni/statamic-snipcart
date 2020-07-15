<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Blueprints\CategoryBlueprint;
use Aerni\Snipcart\Blueprints\ProductBlueprint;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;

class InstallSnipcart extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipcart:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Snipcart';

    /**
     * The default product blueprint title.
     *
     * @var string
     */
    protected $productBlueprintTitle = 'Product';

    /**
     * The default category blueprint title.
     *
     * @var string
     */
    protected $categoryBlueprintTitle = 'Category';

    /**
     * The default collection title.
     *
     * @var string
     */
    protected $productCollectionTitle = 'Products';
    
    /**
     * The default taxonomy title.
     *
     * @var string
     */
    protected $categoryTaxonomyTitle = 'Categories';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // Step 1
        $this->createProductCollection();

        // Step 2
        $this->createCategoryTaxonomy();

        // Make the blueprints, collection and taxonomy.
        $this->makeProductCollection();
        $this->makeProductBlueprint();
        $this->makeCategoryTaxonomy();
        $this->makeCategoryBlueprint();

        // Step 3
        $this->publishVendorFiles();

        // Installation Complete
        $this->complete();
    }

    /**
     * Step 1 – Create the product collection.
     *
     * @return void
     */
    protected function createProductCollection(): void
    {
        $this->info("---  STEP 1 | CREATE THE PRODUCT COLLECTION  ---");
        
        $this->nameProductCollection("Name the collection for your products. Default is '{$this->productCollectionTitle}'.");
        $this->nameProductBlueprint("Name the collection's blueprint. Default is '{$this->productBlueprintTitle}'.");
    }
    
    /**
     * Step 2 – Create the category taxonomy.
     *
     * @return void
     */
    protected function createCategoryTaxonomy(): void
    {
        $this->info("---  STEP 2 | CREATE THE CATEGORY TAXONOMY  ---");
        
        $this->nameCategoryTaxonomy("Name the taxonomy for your product categories. Default is '{$this->categoryTaxonomyTitle}'.");
        $this->nameCategoryBlueprint("Name the taxonomy's blueprint. Default is '{$this->categoryBlueprintTitle}'.");
    }

    /**
     * Step 3 – Publish the vendor files.
     *
     * @return void
     */
    protected function publishVendorFiles(): void
    {
        $this->info('---  STEP 3 | PUBLISH THE VENDOR FILES  ---');

        if ($this->confirm('We need to publish the vendor files. Please confirm.')) {
            Artisan::call('vendor:publish', [
                '--provider' => 'Aerni\Snipcart\ServiceProvider',
                '--force' => true,
            ]);
        }
    }

    /**
     * The final information after a successful installation.
     *
     * @return void
     */
    protected function complete(): void
    {
        $this->info('---  INSTALLATION COMPLETE  ----');
        $this->info('');

        $this->info("The installation was successful!");
    }

    /**
     * Name the product collection.
     *
     * @return void
     */
    protected function nameProductCollection(string $question): void
    {
        $this->productCollectionTitle = $this->ask($question, $this->productCollectionTitle);

        if ($this->hasCollection(Str::snake($this->productCollectionTitle))) {
            $this->error("A collection with the name '{$this->productCollectionTitle}' already exists.");

            if (! $this->confirm("Do you want to override the existing collection?")) {
                $this->nameProductCollection($question);
            }
        }
    }

    /**
     * Name the product blueprint.
     *
     * @return void
     */
    protected function nameProductBlueprint(string $question): void
    {
        $this->productBlueprintTitle = $this->ask($question, $this->productBlueprintTitle);

        if ($this->hasBlueprint(Str::snake($this->productBlueprintTitle))) {
            $this->error("A blueprint with the name '{$this->productBlueprintTitle}' already exists.");

            if (! $this->confirm("Do you want to override the existing blueprint?")) {
                $this->nameProductBlueprint($question);
            }
        }
    }

    /**
     * Name the category taxonomy.
     *
     * @return void
     */
    protected function nameCategoryTaxonomy(string $question): void
    {
        $this->categoryTaxonomyTitle = $this->ask($question, $this->categoryTaxonomyTitle);

        if ($this->hasTaxonomy(Str::snake($this->categoryTaxonomyTitle))) {
            $this->error("A taxonomy with the name '{$this->categoryTaxonomyTitle}' already exists.");

            if (! $this->confirm("Do you want to override the existing taxonomy?")) {
                $this->nameCategoryTaxonomy($question);
            }
        }
    }

    /**
     * Name the category blueprint.
     *
     * @return void
     */
    protected function nameCategoryBlueprint(string $question): void
    {
        $this->categoryBlueprintTitle = $this->ask($question, $this->categoryBlueprintTitle);

        if ($this->hasBlueprint(Str::snake($this->categoryBlueprintTitle))) {
            $this->error("A blueprint with the name '{$this->categoryBlueprintTitle}' already exists.");

            if (! $this->confirm("Do you want to override the existing blueprint?")) {
                $this->nameCategoryBlueprint($question);
            }
        }
    }

    /**
     * Make the product collection.
     *
     * @return void
     */
    protected function makeProductCollection(): void
    {
        Collection::make(Str::snake($this->productCollectionTitle))
            ->title($this->productCollectionTitle)
            ->revisionsEnabled(false)
            ->pastDateBehavior('public')
            ->futureDateBehavior('private')
            ->entryBlueprints(Str::snake($this->productBlueprintTitle))
            ->save();
    }

    /**
     * Make the product blueprint.
     *
     * @return void
     */
    protected function makeProductBlueprint(): void
    {
        (new ProductBlueprint())
            ->taxonomy(Str::snake($this->categoryTaxonomyTitle))
            ->currency(config('snipcart.currency'))
            ->make($this->productBlueprintTitle);
    }

    /**
     * Make the category taxonomy.
     *
     * @return void
     */
    protected function makeCategoryTaxonomy(): void
    {
        Taxonomy::make(Str::snake($this->categoryTaxonomyTitle))
            ->title($this->categoryTaxonomyTitle)
            ->termBlueprints([Str::snake($this->categoryBlueprintTitle)])
            ->collection(Str::snake($this->productCollectionTitle))
            ->save();
    }

    /**
     * Make the category blueprint.
     *
     * @return void
     */
    protected function makeCategoryBlueprint(): void
    {
        (new CategoryBlueprint())
            ->make($this->categoryBlueprintTitle);
    }

    /**
     * Check if a blueprint with the given $handle already exists.
     *
     * @param string $handle
     * @return bool
     */
    protected function hasBlueprint(string $handle): bool
    {
        if (is_null(Blueprint::find($handle))) {
            return false;
        }

        return true;
    }

    /**
     * Check if a collection with the given $handle already exists.
     *
     * @param string $handle
     * @return bool
     */
    protected function hasCollection(string $handle): bool
    {
        if (is_null(Collection::findByHandle($handle))) {
            return false;
        }

        return true;
    }

    /**
     * Check if a taxonomy with the given $handle already exists.
     *
     * @param string $handle
     * @return bool
     */
    protected function hasTaxonomy(string $handle): bool
    {
        if (is_null(Taxonomy::findByHandle($handle))) {
            return false;
        }

        return true;
    }
}
