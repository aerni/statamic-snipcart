<?php

namespace Aerni\Snipcart\Listeners;

use Statamic\Events\EntrySaving;
use Aerni\Snipcart\Facades\VariantsBuilder;
use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;

class BuildProductVariants
{
    use ListenerGuards;

    /**
     * Handle the event.
     *
     * @param EntrySaving $event
     * @return void
     */
    public function handle(EntrySaving $event): void
    {
        if ($this->isSavingProduct($event)) {
            $event->entry->set('variants', VariantsBuilder::process($event->entry));
        }
    }
}
