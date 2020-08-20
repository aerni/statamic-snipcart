<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;
use Statamic\Events\EntryBlueprintFound;

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
        if ($this->isEditingExistingProduct($event)) {
            $content = $event->blueprint->contents();
            $content['sections']['basic']['fields'][3]['field']['read_only'] = true;
            $event->blueprint->setContents($content);
        }
    }
}
