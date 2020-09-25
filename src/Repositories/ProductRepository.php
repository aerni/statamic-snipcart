<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\ProductRepository as ProductRepositoryContract;
use Aerni\Snipcart\Facades\Converter;
use Aerni\Snipcart\Facades\Currency;
use Aerni\Snipcart\Facades\Dimension;
use Aerni\Snipcart\Support\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Statamic\Facades\Image;
use Statamic\Facades\Site;
use Statamic\Support\Str;

class ProductRepository implements ProductRepositoryContract
{
    /**
     * The product entry.
     *
     * @var \Statamic\Entries\Entry
     */
    protected $product;

    protected $customFields;

    /**
     * The selected product variant options.
     *
     * @var array
     */
    protected $selectedVariantOptions;

    /**
     * Set the selected variant options property.
     *
     * @param array $options
     * @return self
     */
    public function selectedVariantOptions(array $options): self
    {
        $this->selectedVariantOptions = $options;

        return $this;
    }

    /**
     * Get the Snipcart attributes from the product entry.
     *
     * @return Collection
     */
    public function processAttributes(\Statamic\Entries\Entry $entry)
    {
        $this->product = $entry;

        $basicFields = $this->mapBasicFields();
        $customFields = $this->mapCustomFields();
        $allFields = $basicFields->merge($customFields);

        return Validator::onlyValidAttributes($allFields);
    }

    /**
     * Merge the product root data with the localized data.
     *
     * @return Collection
     */
    public function data(): Collection
    {
        $localizedData = $this->product->data()->only('price');
        $variants = $this->mergeRootWithLocalizedVariants();

        return $this->root()->data()
            ->merge($localizedData)
            ->merge($variants);
    }

    /**
     * Get the root product variants.
     *
     * @return Collection
     */
    protected function rootVariants(): Collection
    {
        return collect($this->product->root()->get('variants'));
    }

    /**
     * Get the localized product variants.
     *
     * @return Collection
     */
    protected function localizedVariants(): Collection
    {
        return collect($this->product->get('variants'));
    }

    /**
     * Merge the root variants with the localized variants.
     *
     * @return array
     */
    protected function mergeRootWithLocalizedVariants(): array
    {
        $variants = $this->rootVariants()
            ->replaceRecursive($this->localizedVariantPriceModifiers())
            ->all();

        return ['variants' => $variants];
    }

    /**
     * Get an array with the localized variant price modifiers.
     *
     * @return Collection
     */
    protected function localizedVariantPriceModifiers(): Collection
    {
        return $this->localizedVariants()->map(function ($variant) {
            $options = collect($variant['options'])->map(function ($option) {
                return collect($option)->only('price_modifier')->all();
            })->all();

            return ['options' => $options];
        });
    }

    /**
     * Get the root entry of the product.
     *
     * @return \Statamic\Entries\Entry
     */
    protected function root(): \Statamic\Entries\Entry
    {
        return $this->product->root();
    }

    /**
     * Map the attributes to match the format that Snipcart expects.
     *
     * @return Collection
     */
    protected function mapBasicFields(): Collection
    {
        $basicFields = $this->data()->mapWithKeys(function ($basicField, $key) {
            if ($key === 'title') {
                return ['name' => $basicField];
            }

            if ($key === 'sku') {
                return ['id' => $basicField];
            }

            if ($key === 'images' && ! empty($basicField)) {
                return ['image' => $this->imageUrl()];
            }

            if ($key === config('snipcart.taxonomies.categories') && ! empty($basicField)) {
                return ['categories' => $this->mapCategories()];
            }

            if ($key === 'taxes' && ! empty($basicField)) {
                return ['taxes' => $this->mapTaxes()];
            }

            if ($key === 'metadata' && ! empty($basicField)) {
                return [$key => json_encode($basicField)];
            }

            if ($key === 'weight' && ! empty($basicField)) {
                $roundedWeight = (int) round(Converter::toGrams($basicField, $this->weightUnit()));
                return [$key => $roundedWeight];
            }

            if ($key === 'length' && ! empty($basicField)) {
                $roundedLength = (int) round(Converter::toCentimeters($basicField, $this->lengthUnit()));
                return [$key => $roundedLength];
            }

            if ($key === 'width' && ! empty($basicField)) {
                $roundedWidth = (int) round(Converter::toCentimeters($basicField, $this->lengthUnit()));
                return [$key => $roundedWidth];
            }

            if ($key === 'height' && ! empty($basicField)) {
                $roundedHeight = (int) round(Converter::toCentimeters($basicField, $this->lengthUnit()));
                return [$key => $roundedHeight];
            }

            if ($key === 'price' && ! empty($basicField)) {
                return [$key => Currency::from(Site::current())->formatDecimal($basicField)];
            }

            return [$key => $basicField];
        })->mapWithKeys(function ($basicField, $key) {
            return [
                $this->underscoreToDash($key) => (is_bool($basicField)) ? $this->boolToString($basicField) : $basicField,
            ];
        })->put('url', Request::url());

        return $basicFields;
    }

    /**
     * Map the custom fields of the product.
     *
     * @return Collection
     */
    protected function mapCustomFields(): Collection
    {
        $customFields = $this->data()->flatMap(function ($item, $key) {
            if ($key === 'variants') {
                return $this->mapVariants($item);
            }

            if ($key === 'checkboxes') {
                return $this->mapCheckboxes($item);
            }

            if ($key === 'text_fields') {
                return $this->mapTextFields($item);
            }
        })->pipe(function ($customFields) {
            return $this->addCustomFieldIds($customFields);
        });

        return $customFields->collapse();
    }

    /**
     * Returns an array of mapped product variants.
     *
     * @param array $variants
     * @return Collection
     */
    protected function mapVariants(array $variants): Collection
    {
        $variants = collect($variants)->map(function ($variant) {
            $name = $variant['type'];
            $options = $this->mapVariantOptions($variant['options']);
            $value = $this->mapVariantValue($variant['options']);

            return [
                "custom{key}-name" => $name,
                "custom{key}-options" => $options,
                "custom{key}-value" => $value,
            ];
        });

        return $variants;
    }

    /**
     * Returns a string of variant options with modifier price.
     *
     * Small[-1.00]|Medium|Large[+1.00]
     *
     * @param array $options
     * @return string
     */
    protected function mapVariantOptions(array $options): string
    {
        $options = collect($options)->map(function ($option) {
            $name = $option['name'];
            $price = $this->formatPriceModifier($option['price_modifier']);

            return (empty($price))
                ? $name
                : "{$name}[{$price}]";
        });

        return $options->implode('|');
    }

    /**
     * Returns the selected variant option.
     *
     * @param array $options
     * @return string|null
     */
    protected function mapVariantValue(array $options)
    {
        if (empty($this->selectedVariantOptions)) {
            return null;
        }

        $value = collect($options)->flatMap(function ($option) {
            return collect($this->selectedVariantOptions)->filter(function ($selectedOption) use ($option) {
                return $option['name'] === $selectedOption['name']->value();
            })->pluck('name');
        })->first();

        if (! is_null($value)) {
            $value = $value->value();
        }

        return $value;
    }

    /**
     * Format the price modifier of a variant option.
     *
     * null -> null
     * 2000 -> +2.00
     * -2000 -> -2.00
     *
     * @param int $price
     * @return string|null
     */
    protected function formatPriceModifier(?int $price)
    {
        if (empty($price)) {
            return null;
        }

        $decimalPrice = Currency::from(Site::default())->formatDecimal($price);

        return (Str::startsWith($decimalPrice, '-'))
            ? $decimalPrice
            : "+$decimalPrice";
    }

    /**
     * Returns an array of mapped checkboxes.
     *
     * @param array $checkboxes
     * @return Collection
     */
    protected function mapCheckboxes(array $checkboxes): Collection
    {
        $checkboxes = collect($checkboxes)->map(function ($checkbox) {
            return [
                "custom{key}-name" => $checkbox['name'],
                "custom{key}-type" => 'checkbox',
            ];
        });

        return $checkboxes;
    }

    /**
     * Returns an array of mapped text fields.
     *
     * @param array $textFields
     * @return Collection
     */
    protected function mapTextFields(array $textFields): Collection
    {
        $textFields = collect($textFields)->map(function ($textField) {
            return [
                "custom{key}-name" => $textField['name'],
                "custom{key}-type" => $textField['size'] === 'large' ? 'textarea' : '',
                "custom{key}-value" => $textField['default'],
                "custom{key}-placeholder" => $textField['placeholder'],
                "custom{key}-required" => json_encode($textField['required']),
            ];
        });

        return $textFields;
    }

    /**
     * Adds a unique id to all custom fields by replacing {key}.
     *
     * @param Collection $customFields
     * @return Collection
     */
    protected function addCustomFieldIds(Collection $customFields): Collection
    {
        $customFieldsWithId = $customFields->map(function ($customField, $id) {
            $id++;

            return collect($customField)->mapWithKeys(function ($value, $key) use ($id) {
                $keyWithId = Str::replaceFirst('{key}', $id, $key);

                return [$keyWithId => $value];
            })->filter()->all();
        });

        return $customFieldsWithId;
    }

    /**
     * Returns a string of categories.
     *
     * @return string
     */
    protected function mapCategories(): string
    {
        return $this->root()->augmentedValue(config('snipcart.taxonomies.categories'))->value()
            ->filter(function ($category) {
                return ! $category->get('hide_from_snipcart');
            })->map(function ($category) {
                return $category->title();
            })->implode('|');
    }

    /**
     * Returns a string of taxes.
     *
     * @return string
     */
    protected function mapTaxes(): string
    {
        $taxes = $this->root()->augmentedValue('taxes')->value();

        return implode('|', $taxes);
    }

    /**
     * Returns the URL of an image.
     *
     * @return string
     */
    protected function imageUrl(): string
    {
        $imageUrl = $this->root()->augmentedValue('images')->value()[0]->url();

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
        $lengthUnit = $this->root()->value('length_unit');

        if (is_null($lengthUnit)) {
            return Dimension::from(Site::default())
                ->type('length')
                ->short();
        }

        return $lengthUnit;
    }

    /**
     * Returns the weight unit.
     *
     * @return string
     */
    protected function weightUnit(): string
    {
        $weightUnit = $this->root()->value('weight_unit');

        if (is_null($weightUnit)) {
            return Dimension::from(Site::default())
                ->type('weight')
                ->short();
        }

        return $weightUnit;
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
