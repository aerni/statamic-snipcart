<?php

namespace Aerni\Snipcart\Listeners;

use Statamic\Events\EntryBlueprintFound;

class EditingProduct
{
    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(EntryBlueprintFound $event)
    {
        $isProduct = $event->blueprint->handle() === 'product';
        $isEditing = $event->entry;

        if ($isEditing && $isProduct) {
            $content = $event->blueprint->contents();
            $content['sections']['basic']['fields'][2]['field']['read_only'] = true;
            $event->blueprint->setContents($content);
        }
    }
}
