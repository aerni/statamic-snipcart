<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;
use Aerni\SnipcartWebhooks\Events\OrderCompleted;
use Statamic\Facades\Entry;

class UpdateStock
{
    use ListenerGuards;

    public function handle(OrderCompleted $event): void
    {
        $items = $event->order->get('content')['items'];

        $order = collect($items)->mapWithKeys(function ($item) {
            return [ $item['id'] => $item['quantity'] ];
        });

        $order->each(function ($quantity, $sku) {
            $entry = Entry::query()
                ->where('sku', $sku)
                ->where('origin', null)
                ->get()
                ->first();

            $newStock = $entry->get('stock') - $quantity;

            $entry
                ->set('stock', $newStock)
                ->save();
        });
    }
}
