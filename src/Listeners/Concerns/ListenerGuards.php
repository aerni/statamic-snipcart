<?php

namespace Aerni\Snipcart\Listeners\Concerns;

use Statamic\Events\EntryBlueprintFound;
use Statamic\Events\EntrySaving;

trait ListenerGuards
{
    /**
     * Check if you're editing an existing Snipcart product.
     *
     * @param EntryBlueprintFound $event
     * @return bool
     */
    protected function isEditingExistingProduct(EntryBlueprintFound $event): bool
    {
        $collection = config('snipcart.collections.products');
        
        $isRightNamespace = $event->blueprint->namespace() === "collections.{$collection}";
        $isRightHandle = $event->blueprint->handle() === 'product';
        $isEditing = $event->entry;

        $isProduct = $isRightNamespace && $isRightHandle && $isEditing;

        if (! $isProduct) {
            return false;
        }

        return true;
    }
    
    /**
     * Check if you're saving a product.
     *
     * @param EntrySaving $event
     * @return bool
     */
    protected function isSavingProduct(EntrySaving $event): bool
    {
        $collection = config('snipcart.collections.products');

        if ($event->entry->collection()->handle() !== $collection) {
            return false;
        }
        
        return true;
    }
}
