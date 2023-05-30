<?php

namespace Aerni\Snipcart\Data;

use Aerni\Snipcart\Contracts\VariantsBuilder as VariantsBuilderContract;
use Aerni\Snipcart\Fieldtypes\MoneyFieldtype;
use Statamic\Entries\Entry;
use Statamic\Fields\Value;

class VariantsBuilder implements VariantsBuilderContract
{
    protected Entry $entry;

    /**
     * Process the variants build.
     */
    public function process(Entry $entry): ?array
    {
        $this->entry = $entry;

        if (! $this->shouldBuildVariants()) {
            return null;
        }

        return $this->variants();
    }

    /**
     * Returns all possible product variants.
     */
    protected function variants(): array
    {
        return collect(Cartesian::build($this->variations()))
            ->map(fn ($variations) => $this->variant($variations))
            ->all();
    }

    /**
     * Sort and output the variant array.
     */
    protected function variant(array $variations): array
    {
        return [
            'is_variant' => true,
            'base_price' => new Value($this->entry->value('price'), 'base_price', new MoneyFieldtype(), $this->entry),
            'price' => $this->price($variations),
            'variations' => $variations,
        ];
    }

    /**
     * Returns the variations to create a cartesian product from.
     */
    protected function variations(): array
    {
        return collect($this->entry->value('variations'))->map(function ($variation) {
            return collect($variation['options'])->map(fn ($option) => [
                'name' => $variation['name'],
                'option' => $option['name'],
                'price_modifier' => new Value($option['price_modifier'], 'price_modifier', new MoneyFieldtype(), $this->entry),
            ]);
        })->all();
    }

    /**
     * Calculates the total price of a product variant.
     */
    protected function price(array $variation): Value
    {
        $basePrice = $this->entry->value('price');

        $priceModifiers = collect($variation)->map(fn ($option) => $option['price_modifier']->raw());

        $price = $priceModifiers->push($basePrice)->sum();

        return new Value($price, 'price', new MoneyFieldtype(), $this->entry);
    }

    /**
     * Checks if it should build the variants.
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
