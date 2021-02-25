<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\Snipcart\Facades\Dimension;
use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;
use Statamic\Events\EntrySaving;
use Statamic\Facades\Site;

class AddDefaultUnits
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
        if ($this->isSavingProduct($event) && $event->entry->isRoot()) {
            $lengthUnit = Dimension::from(Site::default())->type('length')->short();
            $weightUnit = Dimension::from(Site::default())->type('weight')->short();

            $event->entry->set('length_unit', $lengthUnit);
            $event->entry->set('weight_unit', $weightUnit);
        }
    }
}
