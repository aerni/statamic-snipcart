<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Facades\Currency;
use Aerni\Snipcart\Support\Cartesian;
use Aerni\Snipcart\Support\Helpers;
use Illuminate\Support\Collection;
use Statamic\Facades\Site;

class VariantRepository
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
     * Returns all product variants.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        $productVariants = collect($this->context->get('custom_fields'))
            ->filter(function ($customField) {
                return $customField['type'] === 'dropdown';
            })
            ->map(function ($customField) {
                return [
                    'type' => $customField['name'],
                    'options' => $customField['options'],
                ];
            });

        return Helpers::resetCollectionIndex($productVariants);
    }

    /**
     * Get product variants based on a parameter filter.
     *
     * @return array
     */
    public function get(): array
    {
        $filtered = $this->filter();

        return [
            'options' => $filtered,
            'price' => $this->price($filtered),
        ];
    }

    /**
     * Returns a complete list of all possible product variations.
     *
     * @return Collection
     */
    public function list(): Collection
    {
        $cartesian = Cartesian::build($this->options()->all());

        return collect($cartesian)->map(function ($item) {
            return [
                'options' => $item,
                'price' => $this->price($item),
            ];
        });
    }

    /**
     * Returns all variation options.
     *
     * @return Collection
     */
    protected function options(): Collection
    {
        return $this->all()->map(function ($item, $key) {
            return collect($item['options'])->map(function ($item) use ($key) {
                return [ 'type' => $this->names()[$key] ] + $item;
            })->all();
        });
    }

    /**
     * Calculates the total price of product variants.
     *
     * @param array $item
     * @return string
     */
    protected function price(array $item): string
    {
        $variantsSum = collect($item)->pluck('price')->map(function ($item) {
            return $item->raw();
        })->sum();

        $basePrice = $this->context->raw('price');
        $total = $basePrice + $variantsSum;

        return Currency::from(Site::current())->formatCurrency($total);
    }

    /**
     * Filter the variants based on parameters.
     *
     * @return array
     */
    public function filter(): array
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
     * Returns the variant names.
     *
     * @return Collection
     */
    protected function names(): Collection
    {
        return $this->all()->pluck('type');
    }
}
