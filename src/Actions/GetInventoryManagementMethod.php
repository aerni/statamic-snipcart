<?php

namespace Aerni\Snipcart\Actions;

use Aerni\Snipcart\Facades\ProductApi;
use Statamic\Contracts\Entries\Entry;

class GetInventoryManagementMethod
{
    public static function handle(Entry $entry): ?string
    {
        return ProductApi::find($entry)?->inventoryManagementMethod();
    }
}
