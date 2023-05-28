<?php

namespace Aerni\Snipcart\Listeners\Concerns;

use Statamic\Events\EntryBlueprintFound;
use Statamic\Events\EntrySaving;

trait ListenerGuards
{
    protected function isEditingExistingProduct(EntryBlueprintFound $event): bool
    {
        $collection = config('snipcart.products.collection');

        $isRightNamespace = $event->blueprint->namespace() === "collections.{$collection}";
        $isRightHandle = $event->blueprint->handle() === 'product';
        $isEditing = $event->entry;

        $isProduct = $isRightNamespace && $isRightHandle && $isEditing;

        if (! $isProduct) {
            return false;
        }

        return true;
    }

    protected function isSavingProduct(EntrySaving $event): bool
    {
        return $event->entry->collection()->handle() === config('snipcart.products.collection');
    }
}
