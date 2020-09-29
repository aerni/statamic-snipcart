<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\SnipcartApi\Facades\SnipcartApi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductApiRepository
{
    /**
     * The context.
     *
     * @var \Statamic\Tags\Context
     */
    protected $context;

    /**
     * The product.
     *
     * @var Collection
     */
    protected $product;

    /**
     * Set the context property.
     *
     * @param Collection $context
     * @return self
     */
    public function context(Collection $context): self
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get the product from Snipcart and set the property.
     *
     * @param string $sku
     * @return self
     */
    public function find(string $sku): self
    {
        $this->product = Cache::remember($sku, config('snipcart.api_cache_lifetime'), function () use ($sku) {
            return SnipcartApi::get()
                ->product($sku)
                ->send();
        });

        return $this;
    }

    /**
     * Get the stock of a single product or product variant.
     *
     * @return string
     */
    public function stock(): string
    {
        if ($this->inventoryManagementMethod() === 'Single') {
            return $this->singleStock();
        }

        if ($this->inventoryManagementMethod() === 'Variant') {
            return $this->variantStock();
        }

        return '';
    }

    /**
     * Get the inventory management method.
     *
     * @return string
     */
    public function inventoryManagementMethod(): string
    {
        return $this->product->get('inventoryManagementMethod');
    }

    /**
     * Get the stock of a single product.
     *
     * @return string
     */
    protected function singleStock(): string
    {
        return $this->product->get('stock') ?? '';
    }

    /**
     * Get the stock of a product variant.
     *
     * @return string
     */
    protected function variantStock(): string
    {
        $stock = collect($this->product->get('variants'))->map(function ($variant) {
            $sorted = collect($variant['variation'])->sortBy('name')->values()->toArray();

            if ($this->prepareVariantOptions() === $sorted) {
                return $variant['stock'];
            }
        })
        ->filter()
        ->first();

        return $stock ?? '';
    }

    /**
     * Prepare the variant options from the context
     * to match the variants array that Snipcart's API returns.
     *
     * @return array
     */
    protected function prepareVariantOptions(): array
    {
        return collect($this->context->get('options'))->map(function ($option) {
            return [
                'name' => $option['type'],
                'option' => $option['name'],
            ];
        })
        ->sortBy('name')
        ->values()
        ->toArray();
    }
}
