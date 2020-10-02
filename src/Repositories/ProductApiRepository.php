<?php

namespace Aerni\Snipcart\Repositories;

use Throwable;
use Statamic\Facades\Site;
use Statamic\Facades\Entry;
use Aerni\Snipcart\Data\Product;
use Illuminate\Support\Facades\Cache;
use Aerni\SnipcartApi\Facades\SnipcartApi;
use Illuminate\Support\Collection;

class ProductApiRepository
{
    protected $product;
    protected $entry;
    protected $variant;

    /**
     * Get the product from Snipcart and set the property.
     */
    public function find(string $sku): ?self
    {
        $this->product = Cache::remember($sku, config('snipcart.api_cache_lifetime'), function () use ($sku) {
            return $this->response($sku);
        });

        if ($this->product->isEmpty()) {
            return null;
        }

        $this->entry = $this->entry();

        return $this;
    }

    protected function response(string $sku): Collection
    {
        try {
            return SnipcartApi::get()
                ->product($sku)
                ->send();
        } catch (Throwable $throwable) {
            return collect();
        }
    }

    /**
     * Set the variant.
     */
    public function variant($variations): self
    {
        $this->variant = $variations;

        return $this;
    }

    /**
     * Get the matching product entry.
     */
    protected function entry()
    {
        $entryId = Entry::query()
            ->where('collection', config('snipcart.collections.products'))
            ->where('locale', Site::default()->locale())
            ->where('sku', $this->product->get('userDefinedId'))
            ->get()
            ->first()
            ->id();

        return new Product($entryId);
    }

    /**
     * Get the stock of a single product or product variant.
     */
    public function stock(): ?string
    {
        if ($this->inventoryManagementMethod() === 'Single') {
            return $this->singleStock();
        }

        if ($this->inventoryManagementMethod() === 'Variant') {
            return $this->variantStock();
        }

        return null;
    }

    /**
     * Get the inventory management method.
     */
    public function inventoryManagementMethod(): string
    {
        return $this->product->get('inventoryManagementMethod');
    }

    /**
     * Get the stock of a single product.
     */
    protected function singleStock(): ?string
    {
        return $this->product->get('stock');
    }

    /**
     * Get the stock of a product variant.
     */
    protected function variantStock(): ?string
    {
        $stock = collect($this->product->get('variants'))->map(function ($variant) {
            $variations = collect($variant['variation'])->pluck('option')->sort()->toArray();

            if ($this->rootEntryVariations() === $variations) {
                return $variant['stock'];
            }
        })
        ->filter()
        ->first();

        return $stock;
    }

    /**
     * Get the root entry variations that match the localized variant.
     * This makes sure that the stock also works on localized products.
     */
    protected function rootEntryVariations(): array
    {
        $variations = $this->entry->rootEntryVariants()->map(function ($variant, $variantKey) {
            $variationName = collect($variant['options'])->filter(function ($option, $optionKey) use ($variantKey) {
                $selectedOptionKey = collect($this->variant)->filter(function ($selectedOptions) use ($variantKey, $optionKey) {
                    return $selectedOptions['variant_key'] === $variantKey
                        && $selectedOptions['option_key'] === $optionKey ;
                })->pluck('option_key')->first();

                return $selectedOptionKey === $optionKey;
            })->pluck('name')->first();

            return $variationName;
        })->sort()->toArray();

        return $variations;
    }
}
