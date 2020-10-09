<?php

namespace Aerni\Snipcart\Data;

use Aerni\Snipcart\Contracts\VariantsBuilder as VariantsBuilderContract;
use Aerni\Snipcart\Facades\Currency;
use Aerni\Snipcart\Support\Cartesian;
use Illuminate\Support\Collection;
use Statamic\Facades\Site;

class VariantsBuilder implements VariantsBuilderContract
{
    protected $context;

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
     * Returns a complete list of all possible product variants.
     *
     * @return array
     */
    public function build(): array
    {
        $allPossibleVariants = Cartesian::build($this->variantOptions()->all());

        $variants = collect($allPossibleVariants)->map(function ($options) {
            return $this->variantArray($options);
        })->all();

        return $variants;
    }

    /**
     * Sort and output the variant array.
     *
     * @param array $options
     * @return array
     */
    protected function variantArray(array $options): array
    {
        return [
            'options' => $options,
            'total' => $this->price($options),
        ];
    }

    protected function variations(): Collection
    {
        return $this->rootVariations()
            ->replace($this->localizedVariations());
    }

    protected function rootVariations(): Collection
    {
        return collect($this->context->get('variations')->augmentable()->root()->get('variations'));
    }

    protected function localizedVariations(): Collection
    {
        return collect($this->context->get('variations')->augmentable()->get('variations'));
    }

    protected function variantOptions(): Collection
    {
        return $this->options($this->variations());
    }

    /**
     * Returns all variant options.
     *
     * @return Collection
     */
    protected function options(Collection $variations): Collection
    {
        $options = $variations->map(function ($variation, $variationKey) {
            return collect($variation['options'])->map(function ($option, $optionKey) use ($variation, $variationKey) {
                return [
                    'type' => $variation['name'],
                    'name' => $option['name'],
                    'price_modifier' => $option['price_modifier'],
                    'variation_key' => $variationKey,
                    'option_key' => $optionKey,
                ];
            })->all();
        });

        return $options;
    }

    /**
     * Calculates the total price of a product variant.
     *
     * @param array $options
     * @return string
     */
    protected function price(array $options): string
    {
        $basePrice = $this->context->raw('price');

        $priceModifiers = collect($options)->map(function ($option) {
            return $option['price_modifier'];
        });

        $total = $priceModifiers->push($basePrice)->sum();

        return Currency::from(Site::current())->formatCurrency($total);
    }
}
