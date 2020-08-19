<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Facades\Converter;
use Aerni\Snipcart\Facades\Dimension;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;

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
        $lengthUnit = Dimension::from(Site::default())->type('length')->short();
        $weightUnit = Dimension::from(Site::default())->type('weight')->short();

        Entry::whereCollection($this->products)->each(function ($entry) {
            Converter::convertEntryDimensions($entry);
        });

        $this->line("<info>[✓]</info> Converted length to <comment>{$lengthUnit}</comment>");
        $this->line("<info>[✓]</info> Converted weight to <comment>{$weightUnit}</comment>");
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

        $this->line("<info>[✓]</info> Updated sites in <comment>content/collections/{$this->products}.yaml</comment>");
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
