<?php

namespace Aerni\Snipcart\Data;

use Aerni\Snipcart\Facades\ProductApi;
use Statamic\Contracts\Entries\Entry;

class Variant
{
    public function __construct(protected Entry $entry, protected array $variation)
    {
    }

    public function toArray(): array
    {
        return [
            'base_price' => $this->basePrice(),
            'price' => $this->price(),
            'stock' => $this->stock(),
            'variation' => $this->variation,
        ];
    }

    /**
     * Gets the base price of the product.
     */
    public function basePrice(): int
    {
        return $this->entry->value('price');
    }

    /**
     * Calculates the total price of this product variant.
     */
    public function price(): int
    {
        $priceModifiers = collect($this->variation)
            ->map(fn ($option) => $option['price_modifier']);

        return $priceModifiers->push($this->basePrice())->sum();
    }

    /**
     * Gets the stock of this product variant.
     */
    public function stock(): ?int
    {
        if (! $product = $this->snipcartProduct()) {
            return null;
        }

        if ($product->inventoryManagementMethod() !== 'Variant') {
            return null;
        }

        return collect($product->variants())->map(function ($variant) {
            $variationOptions = collect($variant['variation'])->sort()->flatten()->toArray();

            return $this->rootEntryVariationOptions() === $variationOptions
                ? $variant['stock'] : null;
        })->filter()->first();
    }

    /**
     * Get the root entry variations that match the localized variant.
     * This makes sure that the stock also works on localized products.
     */
    protected function rootEntryVariationOptions(): array
    {
        return $this->product()->rootEntryVariations()->map(function ($variation, $variationKey) {
            $option = collect($variation['options'])->filter(function ($option, $optionKey) use ($variationKey, $variation) {
                $selectedOptionKey = $this->product()->variantWithKeys()->filter(function ($selectedOptions) use ($variationKey, $optionKey) {
                    return $selectedOptions['variation_key'] === $variationKey
                        && $selectedOptions['option_key'] === $optionKey;
                })->pluck('option_key')->first();

                return $selectedOptionKey === $optionKey;
            })->pluck('name')->first();

            return [
                'name' => $variation['name'],
                'option' => $option,
            ];
        })->sort()->flatten()->toArray();
    }

    protected function product(): Product
    {
        return (new Product($this->entry))->variant($this->variation);
    }

    protected function snipcartProduct(): ?SnipcartProduct
    {
        return ProductApi::find($this->entry);
    }
}
