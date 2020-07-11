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
     * The default collection blueprint title.
     *
     * @var string
     */
    protected $collectionBlueprintTitle = 'Product';

    /**
     * The default taxonomy blueprint title.
     *
     * @var string
     */
    protected $taxonomyBlueprintTitle = 'Category';

    /**
     * The default collection title.
     *
     * @var string
     */
    protected $collectionTitle = 'Products';
    
    /**
     * The default taxonomy title.
     *
     * @var string
     */
    protected $taxonomyTitle = 'Categories';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->createBlueprints();
        $this->createContent();
        $this->publishVendorFiles();
        $this->finalInfo();
    }

    protected function createBlueprints(): void
    {
        $this->info("---  STEP 1 | CREATE THE BLUEPRINTS  ---");
        
        $this->createProductBlueprint("Name the product blueprint. Default is '{$this->collectionBlueprintTitle}'.");
        $this->createCategoryBlueprint("Name the category blueprint. Default is '{$this->taxonomyBlueprintTitle}'.");
    }

    protected function createContent(): void
    {
        $this->info("---  STEP 2 | CREATE A COLLECTION AND TAXONOMY  ---");

        $this->createCollection("Name the collection. Default is '{$this->collectionTitle}'.");
        $this->createTaxonomy("Name the taxonomy. Default is '{$this->taxonomyTitle}'.");
    }

    /**
     * Create the product blueprint.
     *
     * @return void
     */
    protected function createProductBlueprint(string $question): void
    {
        $this->collectionBlueprintTitle = $this->ask($question, $this->collectionBlueprintTitle);

        if ($this->hasBlueprint(Str::snake($this->collectionBlueprintTitle))) {

            $this->error("A blueprint with the name '{$this->collectionBlueprintTitle}' already exists.");

            if ($this->confirm("Do you want to override the existing blueprint?")) {
                $this->makeProductBlueprint();
            } else {
                $this->createProductBlueprint("Name the product blueprint. Remember, the default name '{$this->collectionBlueprintTitle}' is already taken.");
            }

        } else {
            $this->makeProductBlueprint();
        }
    }

    /**
     * Create the category blueprint.
     *
     * @return void
     */
    protected function createCategoryBlueprint(string $question): void
    {
        $this->taxonomyBlueprintTitle = $this->ask($question, $this->taxonomyBlueprintTitle);

        if ($this->hasBlueprint(Str::snake($this->taxonomyBlueprintTitle))) {

            $this->error("A blueprint with the name '{$this->taxonomyBlueprintTitle}' already exists.");

            if ($this->confirm("Do you want to override the existing blueprint?")) {
                $this->makeCategoryBlueprint();
            } else {
                $this->createCategoryBlueprint("Name the category blueprint. Remember, the default name '{$this->taxonomyBlueprintTitle}' is already taken.");
            }

        } else {
            $this->makeCategoryBlueprint();
        }
    }

    /**
     * Create the collection.
     *
     * @return void
     */
    protected function createCollection(string $question): void
    {
        $this->collectionTitle = $this->ask($question, $this->collectionTitle);

        if ($this->hasCollection(Str::snake($this->collectionTitle))) {

            $this->error("A collection with the name '{$this->collectionTitle}' already exists.");

            if ($this->confirm("Do you want to override the existing collection?")) {
                $this->makeCollection();
            } else {
                $this->createCollection("Name your collection. Remember, the default name '{$this->collectionTitle}' is already taken.");
            }

        } else {
            $this->makeCollection();
        }
    }

    /**
     * Create the taxonomy.
     *
     * @return void
     */
    protected function createTaxonomy(string $question): void
    {
        $this->taxonomyTitle = $this->ask($question, $this->taxonomyTitle);

        if ($this->hasTaxonomy(Str::snake($this->taxonomyTitle))) {

            $this->error("A taxonomy with the name '{$this->taxonomyTitle}' already exists.");

            if ($this->confirm("Do you want to override the existing taxonomy?")) {
                $this->makeTaxonomy();
            } else {
                $this->createTaxonomy("Name your taxonomy. Remember, the default name '{$this->taxonomyTitle}' is already taken.");
            }

        } else {
            $this->makeTaxonomy();
        }
    }

    /**
     * Publish vendor files.
     *
     * @return void
     */
    protected function publishVendorFiles(): void
    {
        $this->info('---  STEP 3 | PUBLISH VENDOR FILES  ---');

        if ($this->confirm('We need to publish the vendor files. Please confirm.')) {
            
            Artisan::call('vendor:publish',[
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
    protected function finalInfo(): void
    {
        $this->info('---  INSTALLATION COMPLETE  ----');
        $this->info('');

        $this->info("The installation was successful! Make sure to read the documentation on how to set up your views and use the Snipcart tags.");
    }

    /**
     * Make a product blueprint
     *
     * @return void
     */
    protected function makeProductBlueprint(): void
    {
        $blueprint = new ProductBlueprint();
        $blueprint->make($this->collectionBlueprintTitle);
    }

    /**
     * Make a category blueprint
     *
     * @return void
     */
    protected function makeCategoryBlueprint(): void
    {
        $blueprint = new CategoryBlueprint();
        $blueprint->make($this->taxonomyBlueprintTitle);
    }

    /**
     * Make a collection.
     *
     * @return void
     */
    protected function makeCollection(): void
    {
        Collection::make(Str::snake($this->collectionTitle))
            ->title($this->collectionTitle)
            ->revisionsEnabled(false)
            ->pastDateBehavior('public')
            ->futureDateBehavior('private')
            ->entryBlueprints(Str::snake($this->collectionBlueprintTitle))
            ->save();
    }

    /**
     * Make a taxonomy.
     *
     * @return void
     */
    protected function makeTaxonomy(): void
    {
        Taxonomy::make(Str::snake($this->taxonomyTitle))
            ->title($this->taxonomyTitle)
            ->termBlueprints(Str::snake($this->taxonomyBlueprintTitle))
            ->collection(Str::snake($this->collectionTitle))
            ->save();
    }

    /**
     * Return an info with the given $title if $show is true.
     *
     * @param string $title
     * @param boolean $show
     * @return mixed
     */
    protected function title(string $title, bool $show)
    {
        if ($show === true) {
            return $this->info($title);
        }
    }

    /**
     * Check if a blueprint with the given $handle already exists.
     *
     * @param string $handle
     * @return boolean
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
     * @return boolean
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
     * @return boolean
     */
    protected function hasTaxonomy(string $handle): bool
    {
        if (is_null(Taxonomy::findByHandle($handle))) {
            return false;
        }

        return true;
    }
}