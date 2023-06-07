<?php

namespace Aerni\Snipcart\Actions;

use Aerni\Snipcart\Facades\ProductApi;
use Statamic\Contracts\Entries\Entry;

class GetProductVariants
{
    public static function handle(Entry $entry): ?array
    {
        return ProductApi::find($entry)?->variants();
    }
}
