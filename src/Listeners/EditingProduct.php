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
        $this->makeSkuReadOnly($event);
    }

    /**
     * Make the SKU field read only when editing a product.
     *
     * @param EntryBlueprintFound $event
     * @return void
     */
    protected function makeSkuReadOnly(EntryBlueprintFound $event): void
    {
        $collection = config('snipcart.collections.products');
        $isRightNamespace = $event->blueprint->namespace() === "collections.{$collection}";
        $isRightHandle = $event->blueprint->handle() === 'product';

        $isProduct = $isRightNamespace && $isRightHandle;
        $isEditing = $event->entry;

        if ($isEditing && $isProduct) {
            $content = $event->blueprint->contents();
            $content['sections']['basic']['fields'][2]['field']['read_only'] = true;
            $event->blueprint->setContents($content);
        }
    }
}
