<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\SnipcartWebhooks\Events\OrderCompleted;
use Illuminate\Support\Facades\Cache;

class ClearProductApiCache
{
    public function handle(OrderCompleted $event): void
    {
        collect($event->payload->get('content')['items'])
            ->each(fn ($item) => Cache::forget("snipcart-product::{$item['id']}"));
    }
}
