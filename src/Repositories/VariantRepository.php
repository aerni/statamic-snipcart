<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Facades\Currency;
use Illuminate\Support\Collection;
use Aerni\Snipcart\Support\Cartesian;
use Statamic\Facades\Entry;
use Aerni\Snipcart\Facades\Product;
use Statamic\Facades\Site;
use Illuminate\Support\Facades\Request;

class VariantRepository
{
    protected $data;

    /**
     * Set the data property.
     *
     * @param string $id
     */
    public function from(string $id): self
    {
        $this->data = Product::find($id)->data();

        return $this;
    }

    public function all()
    {
        return collect($this->data->get('custom_fields'))
            ->filter(function ($item) {
                return $item['type'] === 'dropdown' && $item['enabled'] === true;
            })
            ->map(function ($item) {
                return [
                    'type' => $item['name'],
                    'options' => $item['options'],
                ];
            });
    }

    public function get(string $key)
    {
        return $this->all()->get($key);
    }

    public function variants(): Collection
    {
        return $this->all()->map(function ($item) {
            return $item['options'];
        });
    }

    public function options(): Collection
    {
        return $this->all()->map(function ($item, $key) {
            return collect($item['options'])->map(function ($item) use ($key) {
                return [ 'type' => $this->names()[$key] ] + $item;
            })->all();
        });
    }

    public function optionsWithPrice(): Collection
    {
        return $this->variants()->map(function ($item) {
            return collect($item)->mapWithKeys(function ($item, $key) {
                return [$key => [
                    $item['name'] => $item['price']
                ]];
            })->all();
        });
    }

    public function cartesian(): Collection
    {
        return collect(Cartesian::build($this->options()->all()));
    }

    protected function calcTotalPrice(string $price): string
    {
        $basePrice = $this->data->get('price');
        $total = $basePrice + $price;

        return Currency::from(Site::current())->formatCurrency($total);
    }

    protected function price(array $item): string
    {
        return $this->calcTotalPrice(
            collect($item)->pluck('price')->sum()
        );
    }

    public function combinations()
    {
        return $this->cartesian()->map(function ($item) {
            if (empty($item)) {
                return $item;
            }

            return [
                'options' => $item,
                'price' => $this->price($item),
            ];
        })->filter();
    }

    public function names(): Collection
    {
        return $this->all()->pluck('type');
    }

    public function combine(?Collection $params)
    {
        return [
            'options' => $this->filter($params),
            'price' => $this->price($this->filter($params))
        ];
    }

    public function filter(?Collection $params)
    {
        return $this->query($params)->flatMap(function ($query) {
            return $this->options()->flatMap(function ($item) use ($query) {
                return collect($item)->filter(function ($item) use ($query) {
                    $sameType = ! strcasecmp($item['type'], $query['type']);
                    $sameName = ! strcasecmp($item['name'], $query['name']);
                    return $sameType && $sameName;
                })->all();
            })->all();
        })->all();
    }

    protected function query(?Collection $params)
    {
        return $params->map(function ($item, $key) {
            return [
                'type' => $key,
                'name' => $item
            ];
        })->values();
    }
}
