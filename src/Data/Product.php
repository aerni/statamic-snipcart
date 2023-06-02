<?php

namespace Aerni\Snipcart\Data;

use Aerni\Snipcart\Contracts\Product as Contract;
use Aerni\Snipcart\Data\Concerns\PreparesProductData;
use Aerni\Snipcart\Support\Validator;
use Illuminate\Support\Collection;
use Statamic\Entries\Entry;
use Statamic\Facades\Site;

class Product implements Contract
{
    use PreparesProductData;

    protected Collection $data;
    protected Collection $params;
    protected Collection $variant;

    public function __construct(protected Entry $entry)
    {
        $this->data = $this->entryData();
    }

    protected function entry(): Entry
    {
        return $this->entry;
    }

    protected function data(): Collection
    {
        return $this->data;
    }

    public function params(Collection $params = null): Collection|self
    {
        if (func_num_args() === 0) {
            return $this->params;
        }

        $this->params = Validator::onlyValidAttributes($params);

        return $this;
    }

    public function variant(array $variations = null): Collection|self
    {
        if (func_num_args() === 0) {
            return $this->variant ?? collect();
        }

        $this->variant = collect($variations);

        return $this;
    }

    public function toHtmlDataString(): string
    {
        return $this->toDataCollection()->map(function ($value, $key) {
            $value = htmlspecialchars(trim($value), ENT_QUOTES);

            return "data-item-{$key}='{$value}'";
        })->implode(' ');
    }

    protected function toDataCollection(): Collection
    {
        return collect([
            'name' => $this->name(),
            'id' => $this->id(),
            'price' => $this->price(),
            'url' => $this->url(),
            'description' => $this->description(),
            'image' => $this->image(),
            'categories' => $this->categories(),
            'file-guid' => $this->fileGuid(),
            'metadata' => $this->metadata(),
            'length' => $this->length(),
            'width' => $this->width(),
            'height' => $this->height(),
            'weight' => $this->weight(),
            'shippable' => $this->shippable(),
            'taxable' => $this->taxable(),
            'has-taxes-included' => $this->hasTaxesIncluded(),
            'taxes' => $this->taxes(),
            'stackable' => $this->stackable(),
            'quantity' => $this->quantity(),
            'quantity-step' => $this->quantityStep(),
            'min-quantity' => $this->minQuantity(),
            'max-quantity' => $this->maxQuantity(),
        ])
        ->merge($this->customFields())
        ->merge($this->params())
        ->filter()
        ->tap(fn ($data) => Validator::validateAttributes($data));
    }

    protected function entryData(): Collection
    {
        return $this->rootEntryData()
            ->merge($this->localizedEntryData())
            ->merge(['variations' => $this->entryVariationsWithLocalizedPriceModifiers()]);
    }

    protected function rootEntryData(): Collection
    {
        return $this->entry()->root()->data();
    }

    protected function localizedEntryData(): Collection
    {
        return $this->entry()->data()->only('price');
    }

    protected function entries(): Collection
    {
        return Site::all()
            ->map(fn ($locale) => $this->entry()->in($locale->handle()))
            ->filter();
    }

    protected function entryVariationsWithLocalizedPriceModifiers(): Collection
    {
        return $this->rootEntryVariations()
            ->replaceRecursive($this->localizedEntryVariationPriceModifiers());
    }

    protected function entryVariations(): Collection
    {
        return $this->rootEntryVariations()
            ->replaceRecursive($this->localizedEntryVariations());
    }

    public function rootEntryVariations(): Collection
    {
        $variations = $this->entry()->root()->get('variations');

        return collect($variations);
    }

    protected function localizedEntryVariations(): Collection
    {
        $variations = $this->entry()->get('variations');

        return collect($variations);
    }

    protected function localizedEntryVariationPriceModifiers(): Collection
    {
        return $this->localizedEntryVariations()->map(function ($variation) {
            $options = collect($variation['options'])->map(function ($option) {
                return collect($option)->only('price_modifier')->all();
            })->all();

            return ['options' => $options];
        });
    }

    public function variantWithKeys(): Collection
    {
        return $this->variant()
            ->replaceRecursive($this->variantKeys());
    }

    protected function variantKeys(): Collection
    {
        return $this->entryVariations()->map(function ($variation) {
            $variationKey = $this->variant()->search(fn ($item) => $item['name'] === $variation['name']);

            $optionKey = collect($variation['options'])
                ->map(fn ($option) => $this->variant()->search(fn ($item) => $item['option'] === $option['name']))
                ->filter(fn ($item) => $item !== false)
                ->keys()
                ->first();

            return [
                'variation_key' => $variationKey,
                'option_key' => $optionKey,
            ];
        });
    }
}
