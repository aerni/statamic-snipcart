<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\Snipcart\Facades\Converter;
use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;
use Statamic\Events\EntryBlueprintFound;

class ConvertDimensions
{
    use ListenerGuards;

    /**
     * Handle the event.
     *
     * @param EntryBlueprintFound $event
     * @return void
     */
    public function handle(EntryBlueprintFound $event): void
    {
        if ($this->isEditingExistingProduct($event)) {
            Converter::convertEntryDimensions($event->entry);
        }
    }
}
