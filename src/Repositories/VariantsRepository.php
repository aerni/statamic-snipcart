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
     * Get product variations based on a parameter filter.
     *
     * @param Collection $params
     * @return array
     */
    public function get(Collection $params): array
    {
        $this->params = $this->params($params);

        $options = $this->filterOptions();

        return [
            'options' => $options,
            'total' => $this->price($options),
        ];
    }

    /**
     * Returns a complete list of all possible product variations.
     *
     * @return array
     */
    public function all(): array
    {
        $cartesian = Cartesian::build($this->options()->all());

        return collect($cartesian)->map(function ($item) {
            return [
                'options' => $item,
                'total' => $this->price($item),
            ];
        })->all();
    }

    /**
     * Returns all product variations.
     *
     * @return Collection
     */
    protected function variations(): Collection
    {
        return collect($this->context->value('variations'));
    }

    /**
     * Returns all variation options.
     *
     * @return Collection
     */
    protected function options(): Collection
    {
        return $this->variations()->map(function ($item, $key) {
            return collect($item['options'])->map(function ($item) use ($key) {
                return [ 'type' => $this->types()[$key] ] + $item;
            })->all();
        });
    }

    /**
     * Filter the variation options based on parameters.
     *
     * @return array
     */
    protected function filterOptions(): array
    {
        return $this->params->flatMap(function ($param) {
            return $this->options()->flatMap(function ($item) use ($param) {
                return collect($item)->filter(function ($item) use ($param) {
                    $sameType = ! strcasecmp($item['type']->value(), $param['type']);
                    $sameName = ! strcasecmp($item['name']->value(), $param['name']);

                    return $sameType && $sameName;
                })->all();
            })->all();
        })->all();
    }

    /**
     * Returns the variation types (names).
     *
     * @return Collection
     */
    protected function types(): Collection
    {
        return $this->variations()->pluck('type');
    }

    /**
     * Calculates the total price of a product variation option.
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
