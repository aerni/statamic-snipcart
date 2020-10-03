<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\SnipcartWebhooks\Events\OrderCompleted;
use Illuminate\Support\Facades\Cache;

class ClearProductApiCache
{
    /**
     * Handle the event.
     *
     * @param OrderCompleted $event
     * @return void
     */
    public function handle(OrderCompleted $event): void
    {
        collect($event->payload->get('content')['items'])->each(function ($item) {
            Cache::forget($item['id']);
        });
    }
}
