<?php

namespace Aerni\Snipcart\Commands;

use Aerni\Snipcart\Facades\Length;
use Aerni\Snipcart\Facades\Weight;
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

            $data = $entry->data();

            $length_unit = $data->get('length_unit');
            $weight_unit = $data->get('weight_unit');

            $length = $data->get('length');
            $width = $data->get('width');
            $height = $data->get('height');
            $weight = $data->get('weight');

            $convertedLength = Length::convert($length, $length_unit);
            $convertedWidth = Length::convert($width, $length_unit);
            $convertedHeight = Length::convert($height, $length_unit);
            $convertedWeight = Weight::convert($weight, $weight_unit);

            $entry->set('length', $convertedLength);
            $entry->set('width', $convertedWidth);
            $entry->set('height', $convertedHeight);
            $entry->set('weight', $convertedWeight);

            $entry->set('length_unit', config('snipcart.length'));
            $entry->set('weight_unit', config('snipcart.weight'));

            $entry->save();

        });

        $this->info("Converted length and weight units in the <comment>{$collection}</comment> collection's entries");
    }
}
