<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\Snipcart\Events\UnitHasChanged;
use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;
use Statamic\Events\EntryBlueprintFound;
use UnitConverter\UnitConverter;

class ConvertUnits
{
    use ListenerGuards;

    protected $entry;

    /**
     * Handle the event.
     *
     * @param EntryBlueprintFound $event
     * @return void
     */
    public function handle(EntryBlueprintFound $event): void
    {
        if (! $this->isProduct($event)) {
            return;
        }

        if (! $this->isEditingExistingProduct($event)) {
            return;
        }

        $this->entry = $event->entry;

        $this->convertValue('length', 'length');
        $this->convertValue('width', 'length');
        $this->convertValue('height', 'length');
        $this->convertValue('weight', 'weight');
    }

    /**
     * Convert a value by passing its key and dimension.
     *
     * @param string $key
     * @param string $dimension
     * @return void
     */
    protected function convertValue(string $key, string $dimension): void
    {
        if ($this->hasValue($key) && $this->canConvertDimension($dimension)) {

            $convertedValue = UnitConverter::default()
                ->convert($this->entry->get($key))
                ->from($this->entry->get("{$dimension}_unit"))
                ->to(config("snipcart.{$dimension}"));

            $this->entry->set($key, $convertedValue);

        }
    }

    /**
     * Check if a value exists for the given key.
     *
     * @param string $key
     * @return boolean
     */
    protected function hasValue(string $key): bool
    {
        if (! $this->entry->get($key)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the dimension can be converted.
     *
     * @param string $dimension
     * @return boolean
     */
    protected function canConvertDimension(string $dimension): bool
    {
        $from = $this->entry->get("{$dimension}_unit");
        $to = config("snipcart.{$dimension}");

        if (! $from) {
            return false;
        }

        if (! $to) {
            return false;
        }

        if ($from === $to) {
            return false;
        }

        return true;
    }
}
