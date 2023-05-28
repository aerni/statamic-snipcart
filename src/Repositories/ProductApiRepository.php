<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\Product as ProductContract;
use Aerni\Snipcart\Data\Product;
use Aerni\SnipcartApi\Facades\SnipcartApi;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Throwable;

class ProductApiRepository
{
    // TODO: Add types
    protected $product;
    protected $entry;
    protected $variant;

    /**
     * Get the product from Snipcart and set the property.
     */
    public function find(string $sku): ?self
    {
        $this->product = Cache::remember($sku, config('snipcart.api_cache_lifetime'), fn () => $this->response($sku));

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
            if ($throwable->getCode() === 404) {
                return collect();
            }

            throw $throwable;
        }
    }

    /**
     * Set the variant.
     * TODO: What type is $variant?
     */
    public function variant($variations): self
    {
        $this->variant = $variations;

        return $this;
    }

    /**
     * Get the matching product entry.
     */
    protected function entry(): ProductContract
    {
        $entryId = Entry::query()
            ->where('collection', config('snipcart.products.collection'))
            ->where('locale', Site::default()->locale()) // TODO: Should this be the default site? What if the root entry was created in another site?
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
        return match ($this->inventoryManagementMethod()) {
            ('Single') => $this->singleStock(),
            ('Variant') => $this->variantStock(),
            default => null
        };
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
        return collect($this->product->get('variants'))
            ->map(function ($variant) {
                $variationOptions = collect($variant['variation'])
                    ->pluck('option')
                    ->sort()
                    ->toArray();

                if ($this->rootEntryVariations() === $variationOptions) {
                    return $variant['stock'];
                }
            })
            ->filter()
            ->first();
    }

    // TODO: Add return type
    protected function variantWithKeys()
    {
        return $this->entry->variant($this->variant)->variantWithKeys();
    }

    /**
     * Get the root entry variations that match the localized variant.
     * This makes sure that the stock also works on localized products.
     */
    protected function rootEntryVariations(): array
    {
        $variations = $this->entry->rootEntryVariations()->map(function ($variation, $variationKey) {
            $variationName = collect($variation['options'])->filter(function ($option, $optionKey) use ($variationKey) {
                $selectedOptionKey = $this->variantWithKeys()->filter(function ($selectedOptions) use ($variationKey, $optionKey) {
                    return $selectedOptions['variation_key'] === $variationKey
                        && $selectedOptions['option_key'] === $optionKey ;
                })->pluck('option_key')->first();

                return $selectedOptionKey === $optionKey;
            })->pluck('name')->first();

            return $variationName;
        })->sort()->toArray();

        return $variations;
    }
}
