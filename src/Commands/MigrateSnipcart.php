<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Facades\Converter;
use Illuminate\Console\Command;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Entry;

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
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->convertUnits();

        $this->info("Snipcart was successfully migrated!");
    }

    protected function convertUnits(): void
    {
        $collection = config('snipcart.collections.products');

        Entry::whereCollection($collection)->each(function ($entry) {
            Converter::convertEntryDimensions($entry);
        });

        $this->info("Converted dimensions in the <comment>{$collection}</comment> collection's entries");
    }
}
