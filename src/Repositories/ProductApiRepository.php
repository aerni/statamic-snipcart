<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Data\SnipcartProduct;
use Aerni\SnipcartApi\Facades\SnipcartApi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Statamic\Contracts\Entries\Entry;
use Throwable;

class ProductApiRepository
{
    /**
     * Get the product from Snipcart and set the property.
     */
    public function find(Entry $entry): ?SnipcartProduct
    {
        $sku = $entry->root()->get('sku');

        return Cache::remember(
            "snipcart-product::{$sku}",
            config('snipcart.api_cache_lifetime'),
            fn () => ($data = $this->response($sku))
                ? new SnipcartProduct($data)
                : null
        );
    }

    protected function response(string $sku): ?Collection
    {
        try {
            return SnipcartApi::get()->product($sku)->send();
        } catch (Throwable $throwable) {
            if ($throwable->getCode() === 404) {
                return null;
            }

            throw $throwable;
        }
    }
}
