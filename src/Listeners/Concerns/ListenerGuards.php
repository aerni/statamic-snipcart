<?php

namespace Aerni\Snipcart\Listeners\Concerns;

use Statamic\Events\EntryBlueprintFound;

trait ListenerGuards
{
    /**
     * Check if the entry is a Snipcart product.
     *
     * @param EntryBlueprintFound $event
     * @return bool
     */
    protected function isProduct(EntryBlueprintFound $event): bool
    {
        $collection = config('snipcart.collections.products');
        
        $isRightNamespace = $event->blueprint->namespace() === "collections.{$collection}";
        $isRightHandle = $event->blueprint->handle() === 'product';

        $isProduct = $isRightNamespace && $isRightHandle;

        if (! $isProduct) {
            return false;
        }

        return true;
    }
    
    /**
     * Check if you're editing an existing Snipcart product.
     *
     * @param EntryBlueprintFound $event
     * @return bool
     */
    protected function isEditingExistingProduct(EntryBlueprintFound $event): bool
    {
        if (! $event->entry) {
            return false;
        }

        return true;
    }
}
