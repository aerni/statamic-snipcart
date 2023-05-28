<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\Snipcart\Facades\VariantsBuilder;
use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;
use Illuminate\Support\Facades\Cache;
use Statamic\Events\EntrySaving;

class BuildProductVariants
{
    use ListenerGuards;

    public function handle(EntrySaving $event): void
    {
        if (! $this->isSavingProduct($event)) {
            return;
        }

        empty($event->entry->get('variations'))
            ? Cache::delete("variants::{$event->entry->id()}")
            : Cache::set("variants::{$event->entry->id()}", VariantsBuilder::process($event->entry));
    }
}
