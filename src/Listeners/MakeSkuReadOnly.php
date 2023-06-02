<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;
use Statamic\Events\EntryBlueprintFound;

class MakeSkuReadOnly
{
    use ListenerGuards;

    public function handle(EntryBlueprintFound $event): void
    {
        if (! $this->isEditingExistingProduct($event)) {
            return;
        }

        $content = $event->blueprint->contents();
        // TODO: Make this smarter by finding the field by handle. So the user can reorder fields without blowing things up.
        $content['tabs']['sidebar']['sections'][0]['fields'][0]['field']['visibility'] = 'read_only';
        $event->blueprint->setContents($content);
    }
}
