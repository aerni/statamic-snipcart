<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Facades\Currency;
use Aerni\Snipcart\Support\Cartesian;
use Illuminate\Support\Collection;
use Statamic\Facades\Site;

class VariantsRepository
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
     * @return Collection
     */
    protected function params(Collection $params): Collection
    {
        return $params->map(function ($item, $key) {
            return [
                'type' => $key,
                'name' => $item,
            ];
        })->values();
    }

    /**
     * Get product variants based on a parameter filter.
     *
     * @param Collection $params
     * @return array
     */
    public function get(Collection $params): array
    {
        $this->params = $this->params($params);

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

        return collect($cartesian)->map(function ($options) {
            return $this->variantArray($options);
        })->all();
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
     * Calculates the total price of a product variant option.
     *
     * @param array $options
     * @return string
     */
    protected function price(array $options): string
    {
        $basePrice = $this->context->raw('price');

        $priceModifier = collect($options)->map(function ($option) {
            return $option['price_modifier']->raw();
        });

        $total = $priceModifier->push($basePrice)->sum();

        return Currency::from(Site::current())->formatCurrency($total);
    }
}
