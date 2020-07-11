<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Blueprints\ProductBlueprint;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection;
use Statamic\Console\RunsInPlease;


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
     * The default blueprint title.
     *
     * @var string
     */
    protected $blueprintTitle = 'Product';

    /**
     * The default collection title.
     *
     * @var string
     */
    protected $collectionTitle = 'Products';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->createBlueprint("Name your blueprint. If you leave this empty we'll call it '{$this->blueprintTitle}'", true);
        $this->createCollection("Name your collection. If you leave this empty we'll call it '{$this->collectionTitle}'.", true);
        $this->publishVendorFiles();
        $this->finalInfo();
    }

    /**
     * Create the blueprint.
     *
     * @return void
     */
    protected function createBlueprint(string $question, bool $showTitle): void
    {
        $this->title("---  STEP 1 | CREATE A BLUEPRINT  ---", $showTitle);

        $this->blueprintTitle = $this->ask($question, $this->blueprintTitle);

        if ($this->hasBlueprint(Str::snake($this->blueprintTitle))) {

            $this->error("A blueprint with the name '{$this->blueprintTitle}' already exists.");

            if ($this->confirm("Do you want to override the existing blueprint?")) {
                $this->makeBlueprint();
            } else {
                $this->createBlueprint("Name your blueprint. Remember, the default name '{$this->blueprintTitle}' is already taken", false);
            }

        } else {
            $this->makeBlueprint();
        }
    }

    /**
     * Create the collection.
     *
     * @return void
     */
    protected function createCollection(string $question, bool $showTitle): void
    {
        $this->title("---  STEP 2 | CREATE A COLLECTION  ---", $showTitle);

        $this->collectionTitle = $this->ask($question, $this->collectionTitle);

        if ($this->hasCollection(Str::snake($this->collectionTitle))) {

            $this->error("A collection with the name '{$this->collectionTitle}' already exists.");

            if ($this->confirm("Do you want to override the existing collection?")) {
                $this->makeCollection();
            } else {
                $this->createCollection("Name your collection. Remember, the default name '{$this->collectionTitle}' is already taken", false);
            }

        } else {
            $this->makeCollection();
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
     * Make a blueprint
     *
     * @return void
     */
    protected function makeBlueprint(): void
    {
        ProductBlueprint::make($this->blueprintTitle);
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
            ->entryBlueprints(Str::snake($this->blueprintTitle))
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
}