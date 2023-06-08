<?php

namespace Aerni\Snipcart\Actions;

use Statamic\Contracts\Entries\Entry;

class GetProductId
{
    public static function handle(Entry $entry): ?string
    {
        return $entry->root()->id();
    }
}
