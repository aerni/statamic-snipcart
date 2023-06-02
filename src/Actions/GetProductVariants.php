<?php

namespace Aerni\Snipcart\Actions;

use Aerni\Snipcart\Data\Variants;
use Illuminate\Support\Collection;
use Statamic\Contracts\Entries\Entry;

class GetProductVariants
{
    public static function handle(Entry $entry): ?Collection
    {
        return (new Variants($entry))->all();
    }
}
