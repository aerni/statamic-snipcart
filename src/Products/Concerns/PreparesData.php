<?php

namespace Aerni\Snipcart\Products\Concerns;

use Statamic\Support\Str;
use Statamic\Facades\Site;
use Statamic\Facades\Image;
use Illuminate\Support\Collection;
use Aerni\Snipcart\Facades\Currency;
use Aerni\Snipcart\Facades\Converter;
use Aerni\Snipcart\Facades\Dimension;
use Illuminate\Support\Facades\Request;

trait PreparesData
{
    protected function name(): string
    {
        return $this->data->get('title');
    }

    protected function id(): string
    {
        return $this->data->get('sku');
    }

    protected function shippable(): string
    {
        return json_encode($this->data->get('shippable'));
    }

    protected function description(): string
    {
        return $this->data->get('description');
    }

    protected function fileGuid(): string
    {
        return $this->data->get('file_guid');
    }

    protected function quantity(): int
    {
        return $this->data->get('quantity');
    }

    protected function quantityStep(): int
    {
        return $this->data->get('quantity_step');
    }

    protected function minQuantity(): int
    {
        return $this->data->get('min_quantity');
    }

    protected function maxQuantity(): int
    {
        return $this->data->get('max_quantity');
    }

    protected function stackable(): string
    {
        return json_encode($this->data->get('stackable'));
    }

    protected function taxable(): string
    {
        return json_encode($this->data->get('taxable'));
    }

    protected function hasTaxesIncluded(): string
    {
        return json_encode($this->data->get('has_taxes_included'));
    }

    protected function taxes(): string
    {
        return implode('|', $this->data->get('taxes'));
    }

    protected function metadata(): string
    {
        return json_encode($this->data->get('metadata'));
    }

    protected function url(): string
    {
        return Request::url();
    }

    protected function price(): string
    {
        $price = $this->data->get('price');

        return Currency::from(Site::current())->formatDecimal($price);
    }

    protected function image(): string
    {
        $imageUrl = $this->entry->augmentedValue('images')->value()[0]->url();

        if (config('snipcart.image.manipulation')) {
            return Image::manipulate($imageUrl, config('snipcart.image.preset'));
        }

        return $imageUrl;
    }

    protected function categories(): string
    {
        $categories = config('snipcart.taxonomies.categories');

        return $this->entry->augmentedValue($categories)->value()
            ->filter(function ($category) {
                return ! $category->get('hide_from_snipcart');
            })->map(function ($category) {
                return $category->title();
            })->implode('|');
    }

    public function customFields(): Collection
    {
        return $this->variants()
            ->merge($this->checkboxes())
            ->merge($this->textFields())
            ->pipe(function ($customFields) {
                return $this->addCustomFieldIds($customFields);
            });
    }

    protected function addCustomFieldIds(Collection $customFields): Collection
    {
        return $customFields->flatMap(function ($customField, $id) {
            $id++;

            return collect($customField)->mapWithKeys(function ($value, $key) use ($id) {
                return [
                    Str::replaceFirst('{key}', $id, $key) => $value
                ];
            });
        });
    }

    protected function checkboxes(): Collection
    {
        $checkboxes = $this->data->get('checkboxes');

        return collect($checkboxes)->map(function ($checkbox) {
            return [
                "custom{key}-name" => $checkbox['name'],
                "custom{key}-type" => 'checkbox',
            ];
        });
    }

    protected function textFields(): Collection
    {
        $textFields = $this->data->get('text_fields');

        return collect($textFields)->map(function ($textField) {
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

    protected function variants(): Collection
    {
        $variants = $this->data->get('variants');

        return collect($variants)->map(function ($variant) {
            return [
                "custom{key}-name" => $variant['type'],
                "custom{key}-options" => $this->variantOptions($variant['options']),
                "custom{key}-value" => $this->variantValue($variant['options']),
            ];
        });
    }

    protected function variantOptions(array $options): string
    {
        return collect($options)->map(function ($option) {
            $name = $option['name'];
            $price = $this->formatPriceModifier($option['price_modifier']);

            return (empty($price))
                ? $name
                : "{$name}[{$price}]";
        })->implode('|');
    }

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

    protected function variantValue(array $options)
    {
        return '';
    }

    // protected function variantValue(array $options)
    // {
    //     if (empty($this->selectedVariantOptions)) {
    //         return null;
    //     }

    //     $value = collect($options)->flatMap(function ($option) {
    //         return collect($this->selectedVariantOptions)->filter(function ($selectedOption) use ($option) {
    //             return $option['name'] === $selectedOption['name']->value();
    //         })->pluck('name');
    //     })->first();

    //     if (! is_null($value)) {
    //         $value = $value->value();
    //     }

    //     return $value;
    // }

    protected function length(): int
    {
        return $this->lengthInCentimeters('length');
    }

    protected function width(): int
    {
        return $this->lengthInCentimeters('width');
    }

    protected function height(): int
    {
        return $this->lengthInCentimeters('height');
    }

    protected function weight(): int
    {
        $weight = $this->data->get('weight');
        $weightInGrams = Converter::toGrams($weight, $this->weightUnit());

        return round($weightInGrams);
    }

    protected function lengthInCentimeters(string $key): int
    {
        $length = $this->data->get($key);
        $lengthInCentimeters = Converter::toCentimeters($length, $this->lengthUnit());

        return round($lengthInCentimeters);
    }

    protected function lengthUnit(): string
    {
        $lengthUnit = $this->entry->get('length_unit');

        if ($lengthUnit === null) {
            return Dimension::from(Site::default())
                ->type('length')
                ->short();
        }

        return $lengthUnit;
    }

    protected function weightUnit(): string
    {
        $weightUnit = $this->entry->get('weight_unit');

        if ($weightUnit === null) {
            return Dimension::from(Site::default())
                ->type('weight')
                ->short();
        }

        return $weightUnit;
    }

    /**
     * Convert a boolean to a string.
     *
     * @param bool $value
     * @return string
     */
    protected function boolToString(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}
