<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Statamic\Facades\Asset;
use Statamic\Facades\Collection as StatamicCollection;
use Statamic\Facades\Entry;

trait ProcessesData
{
    /**
     * All mandatory Snipcart product attributes.
     *
     * @var array
     */
    protected static $requiredAttributes = ['name', 'id', 'price', 'url'];

    /**
     * All optional Snipcart product attributes.
     *
     * @var array
     */
    protected static $optionalAttributes = [
        'description', 'image', 'categories', 'metadata', 'weight', 'length', 'height', 'width', 'quantity', 'max-quantity', 'min-quantity', 'stackable', 'quantity-step', 'shippable', 'taxable', 'taxes', 'has-taxes-included', 'file-guid'
    ];

    /**
     * Join all the Snipcart attributes to an HTML-ready string.
     *
     * @return string
     */
    protected function dataAttributes(): string
    {
        return $this->attributes()->map(function ($value, $key) {
            return "data-item-{$key}='{$value}'";
        })->implode(' ');
    }

    /**
     * Get the Snipcart attributes.
     *
     * @return Collection
     */
    protected function attributes(): Collection
    {
        $attributes = $this->productAttributes()->merge($this->tagAttributes());
        return $this->validateAttributes($attributes);
    }

    /**
     * Get the Snipcart attributes from the product entry.
     *
     * @return Collection
     */
    protected function productAttributes(): Collection
    {
        if (!is_null($this->currentEntry())) {

            $product = $this->currentEntry();
            $data = $product->data();

            $data->put('url', Request::url());
            $data->put('id', $product->id());
            
            return $this->transformAttributes($data);

        }

        return collect();
    }

    /**
     * Transform the attributes to match the format that Snipcart expects.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function transformAttributes(Collection $attributes): Collection
    {
        $transformedValues = $this->transformAttributeValues($attributes);
        $transformedKeys = $this->transformAttributeKeys($transformedValues);
        $validAttributes = $this->filterValidAttributes($transformedKeys);

        return $validAttributes;
    }

    /**
     * Transform the attribute values to match the format that Snipcart expects.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function transformAttributeValues(Collection $attributes): Collection
    {
        return $attributes->map(function ($item, $key) {

            if ($key === 'images' && is_array($item)) {
                return Asset::find("/assets/{$item[0]}")->url();
            }

            if ($key === 'categories' && is_array($item)) {
                return implode('|', $item);
            }

            if ($key === 'taxes' && is_array($item)) {
                return implode('|', $item);
            }
            
            if (Str::startsWith($key, 'custom') && is_array($item)) {
                return implode('|', $item);
            }

            if ($key === 'metadata' && is_array($item)) {
                return json_encode($item);
            }

            return $item;

        });
    }

    /**
     * Transform the attribute keys to match the format that Snipcart expects.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function transformAttributeKeys(Collection $attributes): Collection
    {   
        $transformedKeys = $attributes->mapWithKeys(function ($item, $key) {

            if ($key === 'title') {
                return ['name' => $item];
            }

            if ($key === 'images') {
                return ['image' => $item];
            }

            if (Str::startsWith($key, 'custom_')) {
                $hyphened = Str::of($key)->replace('_', '-');
                return [Str::of($hyphened)->replaceFirst('-', '')->__toString() => $item];
            }

            if (Str::contains($key, '_')) {
                return [Str::of($key)->replace('_', '-')->__toString() => $item];
            }

            return [$key => $item];

        });

        return $transformedKeys;

    }

    /**
     * Filter the attributes to only return valid attributes.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function filterValidAttributes(Collection $attributes): Collection
    {
        return $attributes->filter(function ($item, $key) {
            if ($this->isValidAttributeKey($key)) {
                return $item;
            }
        });
    }

    /**
     * Return true if the key is a valid Snipcart product attribute key.
     *
     * @param string $key
     * @return boolean
     */
    protected function isValidAttributeKey(string $key): bool
    {
        if (in_array($key, Self::$requiredAttributes)) {
            return true;
        }

        if (in_array($key, Self::$optionalAttributes)) {
            return true;
        }

        if (Str::startsWith($key, 'custom')) {
            return true;
        }

        return false;
    }

    /**
     * Return true if results are found in the context.
     *
     * @return boolean
     */
    protected function hasResults(): bool
    {
        if ($this->context->has('no_results')) {
            return false;
        }

        return true;
    }

    /**
     * Get the Snipcart attributes from the tag.
     *
     * @return Collection
     */
    protected function tagAttributes(): Collection
    {
        return $this->params->except(['class', 'text']);
    }

    /**
     * Check if the attributes include Snipcart's mandatory product attributes.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function validateAttributes(Collection $attributes): Collection
    {
        if ($attributes->has(Self::$requiredAttributes)) {
            return $attributes;
        };

        throw new Exception("Please make sure that your products include the mandatory Snipcart attributes: [name], [id], [price], [url]");
    }

    /**
     * Get the products from the products collection.
     */
    protected function products()
    {
        return StatamicCollection::find('products')->queryEntries();
    }

    /**
     * Get the current entry.
     */
    protected function currentEntry()
    {
        return Entry::find($this->get('current', $this->context->get('id')));
    }
}