<?php

namespace Aerni\Snipcart\Data;

use Statamic\Facades\Site;
use Statamic\Facades\Entry;
use Illuminate\Support\Collection;
use Aerni\Snipcart\Support\Validator;
use Aerni\Snipcart\Data\Concerns\PreparesProductData;
use Aerni\Snipcart\Contracts\Product as ProductContract;

class Product implements ProductContract
{
    use PreparesProductData;

    protected $entry;
    protected $data;
    protected $params;
    protected $selectedVariants;

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

    public function selectedVariants(array $options = null)
    {
        if (func_num_args() === 0) {
            return $this->selectedVariants;
        }

        $this->selectedVariants = collect($options);

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
            ->merge($this->entryVariants());
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

    protected function entryVariants(): array
    {
        $variants = $this->rootEntryVariants()
            ->replaceRecursive($this->localizedEntryVariantPriceModifiers())
            ->all();

        return ['variants' => $variants];
    }

    public function rootEntryVariants(): Collection
    {
        return collect($this->entry()->root()->get('variants'));
    }

    protected function localizedEntryVariants(): Collection
    {
        $locale = Site::current()->handle();

        return collect($this->entry()->in($locale)->get('variants'));
    }

    protected function localizedEntryVariantPriceModifiers(): Collection
    {
        return $this->localizedEntryVariants()->map(function ($variant) {
            $options = collect($variant['options'])->map(function ($option) {
                return collect($option)->only('price_modifier')->all();
            })->all();

            return ['options' => $options];
        });
    }
}
