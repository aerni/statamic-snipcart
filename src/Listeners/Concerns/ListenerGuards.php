<?php

namespace Aerni\Snipcart\Listeners\Concerns;

use Statamic\Events\EntryBlueprintFound;
use Statamic\Events\EntrySaving;

trait ListenerGuards
{
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

    protected function isSavingProduct(EntrySaving $event): bool
    {
        $collection = config('snipcart.collections.products');

        if ($event->entry->collection()->handle() !== $collection) {
            return false;
        }

        return true;
    }
}
