<?php

namespace Aerni\Snipcart\Data;

use Aerni\Snipcart\Contracts\Product as ProductContract;
use Aerni\Snipcart\Data\Concerns\PreparesProductData;
use Aerni\Snipcart\Support\Validator;
use Illuminate\Support\Collection;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;

class Product implements ProductContract
{
    use PreparesProductData;

    protected $entry;
    protected $data;
    protected $params;
    protected $variant;

    public function __construct(string $id)
    {
        $this->entry = Entry::find($id);
        $this->data = $this->entryData();
    }

    protected function entry(): \Statamic\Entries\Entry
    {
        return $this->entry;
    }

    protected function data(): Collection
    {
        return $this->data;
    }

    public function params(Collection $params = null)
    {
        if (func_num_args() === 0) {
            return $this->params;
        }

        $this->params = Validator::onlyValidAttributes($params);

        return $this;
    }

    public function variant(array $variations = null)
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
        $data = collect([
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
        ->filter();

        return Validator::validateAttributes($data);
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
        $locale = Site::current()->handle();

        return $this->entry()->in($locale)->data()
            ->only('price');
    }

    protected function entries(): Collection
    {
        return Site::all()->map(function ($locale) {
            return $this->entry()->in($locale->handle());
        })->filter();
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
        return collect($this->entry()->root()->get('variations'));
    }

    protected function localizedEntryVariations(): Collection
    {
        $locale = Site::current()->handle();

        return collect($this->entry()->in($locale)->get('variations'));
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
}
