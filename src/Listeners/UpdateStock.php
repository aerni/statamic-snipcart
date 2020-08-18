<?php

namespace Aerni\Snipcart\Listeners;

use Aerni\SnipcartWebhooks\Events\OrderCompleted;
use Aerni\Snipcart\Listeners\Concerns\ListenerGuards;
use Statamic\Facades\Entry;

class UpdateStock
{
    use ListenerGuards;

    /**
     * Handle the event.
     *
     * @param OrderCompleted $event
     * @return void
     */
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
