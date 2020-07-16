<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Collection as StatamicCollection;
use Statamic\Facades\Entry;
use Statamic\Facades\Image;

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
        'description', 'image', 'categories', 'metadata', 'weight', 'length', 'height', 'width', 'quantity', 'max-quantity', 'min-quantity', 'stackable', 'quantity-step', 'shippable', 'taxable', 'taxes', 'has-taxes-included', 'file-guid',
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
        if (! is_null($this->currentEntry())) {
            $product = $this->currentEntry();
            $data = $product->data();

            $data->put('url', Request::url());
            $data->put('id', $this->productId($product));

            return $this->transformAttributes($data);
        }

        return collect();
    }

    /**
     * Get the product's ID.
     *
     * @param \Statamic\Entries\Entry $product
     * @return string
     */
    protected function productId(\Statamic\Entries\Entry $product): string
    {
        return $product->data()->get('product_id') ?? $product->id();
    }

    /**
     * Transform the attributes to match the format that Snipcart expects.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function transformAttributes(Collection $attributes): Collection
    {
        $transformedAttributes = $attributes->mapWithKeys(function ($item, $key) {
            if ($key === 'title') {
                return ['name' => $item];
            }

            if ($key === 'images' && is_array($item)) {
                return ['image' => $this->glideImagePath($item[0])];
            }

            if ($key === 'categories' && is_array($item)) {
                return [$key => implode('|', $item)];
            }

            if ($key === 'taxes' && is_array($item)) {
                return [$key => implode('|', $item)];
            }
            
            if ($key === 'custom_fields' && is_array($item)) {
                return $this->mapCustomFields($item);
            }

            if ($key === 'metadata' && is_array($item)) {
                return [$key => json_encode($item)];
            }

            return [$key => $item];
        });

        return $this->filterValidAttributes($transformedAttributes);
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
     * Transform the custom fields to fit the expected format.
     *
     * @param array $customFields
     * @return array
     */
    protected function mapCustomFields(array $customFields): Collection
    {
        $customFields = collect($customFields)->map(function ($item, $key) {
            $key++;

            if (! $item['enabled']) {
                return;
            }

            if ($item['type'] === 'dropdown') {
                return $this->mapDropdown($item, $key);
            }

            if ($item['type'] === 'checkbox') {
                return $this->mapCheckbox($item, $key);
            }

            if ($item['type'] === 'text' && $item['field_type'] === 'text') {
                return $this->mapTextField($item, $key);
            }

            if ($item['type'] === 'text' && $item['field_type'] === 'textarea') {
                return $this->mapTextarea($item, $key);
            }
        })->filter()->values();

        return $this->reassignFieldIndex($customFields);
    }

    /**
     * Make sure to start properly index custom fields starting with 1.
     *
     * @param Collection $customFields
     * @return Collection
     */
    protected function reassignFieldIndex(Collection $customFields): Collection
    {
        return $customFields->flatMap(function ($item, $index) {
            $index++;

            return collect($item)->mapWithKeys(function ($item, $key) use ($index) {
                $newKey = preg_replace("/[0-9]/", $index, $key);

                return [$newKey => $item];
            });
        });
    }

    /**
     * Map dropdowns to the proper structure.
     *
     * @param array $item
     * @param string $key
     * @return array
     */
    protected function mapDropdown(array $item, string $key): array
    {
        $options = collect($item['options'])->map(function ($item) {
            $name = $item['name'];
            $price = $item['price'];

            if (empty($price)) {
                return $name;
            }
            
            if (! Str::startsWith($price, ['+', '-'])) {
                $price = "+{$price}";
            }
            
            return "{$name}[{$price}]";
        })->implode('|');

        return [
            "custom{$key}-name" => $item['name'],
            "custom{$key}-options" => $options,
        ];
    }

    /**
     * Map checkboxes to the proper structure.
     *
     * @param array $item
     * @param string $key
     * @return array
     */
    protected function mapCheckbox(array $item, string $key): array
    {
        return [
            "custom{$key}-name" => $item['name'],
            "custom{$key}-type" => 'checkbox',
            "custom{$key}-value" => json_encode($item['default']),
        ];
    }

    /**
     * Map text fields to the proper structure.
     *
     * @param array $item
     * @param string $key
     * @return array
     */
    protected function mapTextField(array $item, string $key): array
    {
        return [
            "custom{$key}-name" => $item['name'],
            "custom{$key}-value" => $item['default'],
            "custom{$key}-placeholder" => $item['placeholder'],
            "custom{$key}-required" => json_encode($item['required']),
        ];
    }

    /**
     * Map textarea fields to the proper structure.
     *
     * @param array $item
     * @param string $key
     * @return array
     */
    protected function mapTextarea(array $item, string $key): array
    {
        return [
            "custom{$key}-name" => $item['name'],
            "custom{$key}-type" => $item['field_type'],
            "custom{$key}-value" => $item['default'],
            "custom{$key}-placeholder" => $item['placeholder'],
            "custom{$key}-required" => json_encode($item['required']),
        ];
    }

    /**
     * Get the URL of an image.
     *
     * @param string $image
     * @return string
     */
    protected function imagePath(string $image): string
    {
        $blueprint = $this->currentEntry()->blueprint()->contents()['sections'];

        $imageField = collect($blueprint)->flatMap(function ($item) {
            return collect($item['fields'])->first(function ($item) {
                return $item['handle'] === 'images';
            });
        });

        $assetContainer = $imageField['field']['container'];

        $containerPath = AssetContainer::find($assetContainer)->url();

        return "{$containerPath}/$image";
    }

    /**
     * Get the glide path for an image.
     *
     * @param string $image
     * @return string
     */
    protected function glideImagePath(string $image): string
    {
        $imagePath = $this->imagePath($image);

        if (config('snipcart.image.manipulation')) {
            return Image::manipulate($imagePath, config('snipcart.image.preset'));
        }

        return $imagePath;
    }

    /**
     * Return true if the key is a valid Snipcart product attribute key.
     *
     * @param string $key
     * @return bool
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
     * @return bool
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
