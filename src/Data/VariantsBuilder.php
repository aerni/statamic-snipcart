<?php

namespace Aerni\Snipcart\Data;

use Aerni\Snipcart\Contracts\VariantsBuilder as VariantsBuilderContract;
use Illuminate\Support\Collection;
use Statamic\Entries\Entry;

class VariantsBuilder implements VariantsBuilderContract
{
    protected $entry;

    /**
     * Process the variants build.
     *
     * @param Entry $entry
     * @return array|null
     */
    public function process(Entry $entry): ?array
    {
        $this->entry = $entry;

        if (! $this->shouldBuildVariants()) {
            return null;
        }

        return $this->build();
    }

    /**
     * Returns a complete list of all possible product variants.
     *
     * @return array
     */
    protected function build(): array
    {
        $cartesian = Cartesian::build($this->variations());

        return collect($cartesian)->map(function ($variations) {
            return $this->variant(collect($variations));
        })->all();
    }

    /**
     * Sort and output the variant array.
     *
     * @param Collection $variations
     * @return array
     */
    protected function variant(Collection $variations): array
    {
        $price = $this->price($variations);

        $variations = $variations->map(function ($variation) {
            return [
                'name' => $variation['name'],
                'option' => $variation['option'],
            ];
        })->all();

        return [
            'price' => $price,
            'variations' => $variations,
        ];
    }

    /**
     * Returns the variations to create a cartesian product from.
     *
     * @return array
     */
    protected function variations(): array
    {
        $variations = $this->entry->get('variations') ?? $this->entry->root()->get('variations');

        return collect($variations)->map(function ($variation) {
            return collect($variation['options'])->map(function ($option) use ($variation) {
                return [
                    'name' => $variation['name'],
                    'option' => $option['name'],
                    'price_modifier' => $option['price_modifier'],
                ];
            });
        })->all();
    }

    /**
     * Calculates the total price of a product variant.
     *
     * @param Collection $variations
     * @return int
     */
    protected function price(Collection $variations): int
    {
        $basePrice = $this->entry->get('price') ?? $this->entry->root()->get('price');

        $priceModifiers = $variations->map(function ($variation) {
            return $variation['price_modifier'];
        });

        return $priceModifiers->push($basePrice)->sum();
    }

    /**
     * Checks if it should build the variants.
     *
     * @return bool
     */
    protected function shouldBuildVariants(): bool
    {
        if ($this->entry->get('price')) {
            return true;
        }

        if ($this->entry->get('variations')) {
            return true;
        }

        return false;
    }
}
