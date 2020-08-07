<?php

namespace Aerni\Snipcart\Listeners;

use Statamic\Events\EntryBlueprintFound;
use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;

class MakeSkuReadOnly
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
        if (! $this->isProduct($event)) {
            return;
        }

        if (! $this->isEditingExistingProduct($event)) {
            return;
        }

        $content = $event->blueprint->contents();
        $content['sections']['basic']['fields'][2]['field']['read_only'] = true;
        $event->blueprint->setContents($content);
    }
}
