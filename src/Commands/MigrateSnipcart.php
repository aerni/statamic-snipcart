<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Facades\Converter;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Facades\Collection;

class MigrateSnipcart extends Command
{
    use RunsInPlease;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snipcart:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Snipcart';

    /**
     * The products collection handle
     *
     * @var string
     */
    protected $products;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->products = config('snipcart.collections.products');

        $this->convertUnits();
        $this->updateSites();

        $this->info("Snipcart was successfully migrated!");
    }

    /**
     * Convert the length/weight units in the product's root entry.
     *
     * @return void
     */
    protected function convertUnits(): void
    {
        Entry::whereCollection($this->products)->each(function ($entry) {
            Converter::convertEntryDimensions($entry);
        });

        $this->info("Converted the length and weight dimensions of the <comment>{$this->products}</comment> entries.");
    }

    /**
     * Update the sites array of the product's collection.
     *
     * @return void
     */
    protected function updateSites(): void
    {
        Collection::find($this->products)
            ->sites($this->sites())
            ->save();

        $this->info("Updated the sites array in the <comment>{$this->products}</comment> configuration.");
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
