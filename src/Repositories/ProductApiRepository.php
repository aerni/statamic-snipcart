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
        if (! $sku = $entry->root()->get('sku')) {
            return null;
        }

        $response = Cache::remember("snipcart-product::{$sku}", config('snipcart.api_cache_lifetime'), fn () => $this->response($sku));

        return $response->isNotEmpty()
            ? new SnipcartProduct($response)
            : null;
    }

    protected function response(string $sku): Collection
    {
        try {
            return SnipcartApi::get()->product($sku)->send();
        } catch (Throwable $throwable) {
            if ($throwable->getCode() === 404) {
                return collect();
            }

            throw $throwable;
        }
    }
}
