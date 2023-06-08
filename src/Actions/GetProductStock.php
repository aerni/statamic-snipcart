<?php

namespace Aerni\Snipcart\Actions;

use Aerni\Snipcart\Facades\ProductApi;
use Statamic\Contracts\Entries\Entry;

class GetProductStock
{
    public static function handle(Entry $entry): ?int
    {
        if (! $product = ProductApi::find($entry)) {
            return null;
        }

        return match ($product->inventoryManagementMethod()) {
            ('Single') => $product->stock(),
            ('Variant') => $product->totalStock(),
            default => null
        };
    }
}
