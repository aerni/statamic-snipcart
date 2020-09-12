<?php

namespace Aerni\Snipcart\Support;

use Illuminate\Support\Collection;

class Helpers
{
    /**
     * Returns a new collection starting at index 0.
     *
     * @param Collection $collection
     * @return Collection
     */
    public static function resetCollectionIndex(Collection $collection): Collection
    {
        return $collection->values();
    }
}
