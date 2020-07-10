<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Blueprints\ProductBlueprint;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
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
    protected $description = 'Let Statamic Snipcart help you through the setup process.';

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
        $this->makeBlueprint();
        $this->makeCollection();
        $this->publishVendorFiles();
    }

    /**
     * Create the blueprint.
     *
     * @return void
     */
    protected function makeBlueprint(): void
    {
        $this->info('---  STEP 1 | CREATE A BLUEPRINT  ---');

        $this->blueprintTitle = $this->ask("Name your blueprint. If you leave this empty we'll call it", $this->blueprintTitle);

        new ProductBlueprint($this->blueprintTitle);
    }

    /**
     * Create the collection.
     *
     * @return void
     */
    protected function makeCollection(): void
    {
        $this->info('---  STEP 2 | CREATE A COLLECTION  ---');

        $this->collectionTitle = $this->ask("Name your collection. If you leave this empty we'll call it", $this->collectionTitle);

        Collection::make(Str::snake($this->collectionTitle))
            ->title($this->collectionTitle)
            ->revisionsEnabled(false)
            ->pastDateBehavior('public')
            ->futureDateBehavior('private')
            ->entryBlueprints(Str::snake($this->blueprintTitle))
            ->save();
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
    
            $this->info('Config: config/snipcart.php');
            $this->info('Lang: resources/lang/vendor/snipcart/');

        }
    }
}