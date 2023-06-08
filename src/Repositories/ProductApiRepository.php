<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Actions\GetProductId;
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
        if (! $id = GetProductId::handle($entry)) {
            return null;
        }

        $response = Cache::remember("snipcart-product::{$id}", config('snipcart.api_cache_lifetime'), fn () => $this->response($id));

        return $response->isNotEmpty()
            ? new SnipcartProduct($response)
            : null;
    }

    protected function response(string $id): Collection
    {
        try {
            return SnipcartApi::get()->product($id)->send();
        } catch (Throwable $throwable) {
            if ($throwable->getCode() === 404) {
                return collect();
            }

            throw $throwable;
        }
    }
}
