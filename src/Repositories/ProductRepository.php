<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\ProductRepository as ProductRepositoryContract;
use Aerni\Snipcart\Facades\Converter;
use Aerni\Snipcart\Facades\Currency;
use Aerni\Snipcart\Facades\Dimension;
use Aerni\Snipcart\Support\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Image;
use Statamic\Facades\Site;
use Statamic\Support\Str;

class ProductRepository implements ProductRepositoryContract
{
    /**
     * The product entry.
     *
     * @var Entry
     */
    protected $product;

    /**
     * The product data.
     *
     * @var Collection
     */
    protected $data;

    /**
     * Get the product entry and data by its id.
     *
     * @param string $id
     * @return self
     */
    public function find(string $id): self
    {
        $this->product = EntryFacade::find($id);
        $this->data = $this->data();

        return $this;
    }

    /**
     * Get the products data.
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->product->root()->data()
            ->merge($this->product->data());
    }

    /**
     * Get the Snipcart attributes from the product entry.
     *
     * @return Collection
     */
    public function attributes(): Collection
    {
        $attributes = $this->mapAttributes($this->data);
        $attributes->put('url', Request::url());

        return Validator::onlyValidAttributes($attributes);
    }

    /**
     * Map the attributes to match the format that Snipcart expects.
     *
     * @param Collection $data
     * @return Collection
     */
    protected function mapAttributes(Collection $data): Collection
    {
        $mappedAttributes = $data->mapWithKeys(function ($item, $key) {
            if ($key === 'title') {
                return ['name' => $item];
            }

            if ($key === 'sku') {
                return ['id' => $item];
            }

            if ($key === 'images' && ! empty($item)) {
                return ['image' => $this->imageUrl()];
            }

            if ($key === config('snipcart.taxonomies.categories') && ! empty($item)) {
                return ['categories' => $this->mapCategories()];
            }

            if ($key === config('snipcart.taxonomies.taxes') && ! empty($item)) {
                return ['taxes' => $this->mapTaxes()];
            }

            if ($key === 'custom_fields' && ! empty($item)) {
                return $this->mapCustomFields($item);
            }

            if ($key === 'metadata' && ! empty($item)) {
                return [$key => json_encode($item)];
            }

            if ($key === 'weight' && ! empty($item)) {
                return [$key => Converter::toGrams($item, $this->weightUnit())];
            }

            if ($key === 'length' && ! empty($item)) {
                return [$key => Converter::toCentimeters($item, $this->lengthUnit())];
            }

            if ($key === 'width' && ! empty($item)) {
                return [$key => Converter::toCentimeters($item, $this->lengthUnit())];
            }

            if ($key === 'height' && ! empty($item)) {
                return [$key => Converter::toCentimeters($item, $this->lengthUnit())];
            }

            if ($key === 'price' && ! empty($item)) {
                return [$key => Currency::from(Site::current())->formatDecimal($item)];
            }

            return [$key => $item];
        })->mapWithKeys(function ($item, $key) {
            if (is_bool($item)) {
                return [
                    $this->underscoreToDash($key) => $this->boolToString($item),
                ];
            }

            return [
                $this->underscoreToDash($key) => $item,
            ];
        });

        return $mappedAttributes;
    }

    /**
     * Get the Snipcart categories.
     *
     * @return string
     */
    protected function mapCategories(): string
    {
        return $this->product->augmentedValue('categories')->value()
            ->filter(function ($item) {
                return ! $item->data()->get('hide_from_snipcart');
            })->map(function ($item) {
                return $item->title();
            })->implode('|');
    }

    /**
     * Get the Snipcart taxes.
     *
     * @return string
     */
    protected function mapTaxes(): string
    {
        return $this->product->augmentedValue('taxes')->value()
            ->map(function ($item) {
                return $item->title();
            })->implode('|');
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
            $price = $this->calcPriceDifference($item['price']);

            if (empty($price)) {
                return $name;
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
     * Calculate the price difference between the original price and a variant price.
     *
     * @param mixed $price
     * @return mixed
     */
    protected function calcPriceDifference($price)
    {
        if (array_key_exists('price', $this->data->toArray())) {
            if (is_null($price)) {
                return null;
            }

            $originalPrice = $this->data['price'];

            if ($originalPrice === $price) {
                return null;
            }

            $priceDifference = Currency::from(Site::current())->formatDecimal($price - $originalPrice);

            if (Str::startsWith($priceDifference, '-')) {
                return $priceDifference;
            }

            return "+{$priceDifference}";
        }
    }

    /**
     * Get the URL of an image.
     *
     * @return string
     */
    protected function imageUrl(): string
    {
        $imageUrl = $this->product->augmentedValue('images')->value()[0]->url();

        if (config('snipcart.image.manipulation')) {
            return Image::manipulate($imageUrl, config('snipcart.image.preset'));
        }

        return $imageUrl;
    }

    /**
     * Returns the length unit.
     *
     * @return string
     */
    protected function lengthUnit(): string
    {
        if ($this->data->has('length_unit')) {
            return $this->data->get('length_unit');
        }

        return Dimension::from(Site::default())
            ->type('length')
            ->short();
    }

    /**
     * Returns the weight unit.
     *
     * @return string
     */
    protected function weightUnit(): string
    {
        if ($this->data->has('weight_unit')) {
            return $this->data->get('weight_unit');
        }

        return Dimension::from(Site::default())
            ->type('weight')
            ->short();
    }

    /**
     * Replaces underscores with dashes.
     *
     * @param string $item
     * @return string
     */
    protected function underscoreToDash(string $item): string
    {
        return str_replace('_', '-', $item);
    }

    /**
     * Convert a boolean to a string.
     *
     * @param bool $item
     * @return string
     */
    protected function boolToString(bool $item): string
    {
        return $item ? 'true' : 'false';
    }
}
