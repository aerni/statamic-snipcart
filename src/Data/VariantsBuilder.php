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
    protected $params;

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
     * Set the params property
     *
     * @param Collection $params
     * @return self
     */
    public function params(Collection $params): self
    {
        $this->params = $params->map(function ($item, $key) {
            return [
                'type' => $key,
                'name' => $item,
            ];
        })->values();

        return $this;
    }

    /**
     * Get product variants based on a parameter filter.
     *
     * @return array
     */
    public function get(): array
    {
        $options = $this->filterOptions();

        return $this->variantArray($options);
    }

    /**
     * Returns a complete list of all possible product variants.
     *
     * @return array
     */
    public function all(): array
    {
        $cartesian = Cartesian::build($this->options()->all());

        $completeList = collect($cartesian)->map(function ($options) {
            return $this->variantArray($options);
        })->all();

        return $completeList;
    }

    /**
     * Sort and output the variant array.
     *
     * @param array $options
     * @return array
     */
    protected function variantArray(array $options): array
    {
        $sortedOptions = collect($options)->sortBy(function ($option) {
            return $option['type']->value();
        })->values()->all();

        return [
            'options' => $sortedOptions,
            'total' => $this->price($sortedOptions),
        ];
    }

    /**
     * Returns all product variants.
     *
     * @return Collection
     */
    protected function variants(): Collection
    {
        return collect($this->context->value('variants'));
    }

    /**
     * Returns all variant options.
     *
     * @return Collection
     */
    protected function options(): Collection
    {
        return $this->variants()->map(function ($variant, $key) {
            return collect($variant['options'])->map(function ($option) use ($key) {
                return [ 'type' => $this->types()[$key] ] + $option;
            })->all();
        });
    }

    /**
     * Filter the variant options based on parameters.
     *
     * @return array
     */
    protected function filterOptions(): array
    {
        return $this->params->flatMap(function ($param) {
            return $this->options()->flatMap(function ($options) use ($param) {
                return collect($options)->filter(function ($option) use ($param) {
                    $sameType = ! strcasecmp($option['type']->value(), $param['type']);
                    $sameName = ! strcasecmp($option['name']->value(), $param['name']);

                    return $sameType && $sameName;
                })->all();
            })->all();
        })->all();
    }

    /**
     * Returns the variant types.
     *
     * @return Collection
     */
    protected function types(): Collection
    {
        return $this->variants()->pluck('type');
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
            return $option['price_modifier']->raw();
        });

        $total = $priceModifiers->push($basePrice)->sum();

        return Currency::from(Site::current())->formatCurrency($total);
    }
}
