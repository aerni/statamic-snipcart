<?php

namespace Aerni\Snipcart\Data\Concerns;

use Aerni\Snipcart\Facades\Converter;
use Aerni\Snipcart\Facades\Currency;
use Aerni\Snipcart\Facades\Dimension;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Statamic\Facades\Image;
use Statamic\Facades\Site;
use Statamic\Support\Str;

trait PreparesProductData
{
    protected function name(): ?string
    {
        return $this->data()->get('title');
    }

    protected function id(): ?string
    {
        return $this->data()->get('sku');
    }

    protected function price(): ?string
    {
        if (count($this->currencies()->unique()) === 1) {
            return $this->simpleCurrencyPrice();
        };

        return $this->multiCurrencyPrices();
    }

    protected function simpleCurrencyPrice(): string
    {
        $price = $this->data()->get('price');

        return Currency::from(Site::current())->formatDecimal($price);
    }

    protected function multiCurrencyPrices(): string
    {
        $prices = $this->entries()->map(function ($entry) {
            $currency = $this->currencies()->get($entry->locale());
            $price = Currency::from($entry->site())->formatDecimal($entry->get('price'));

            return [
                'currency' => Str::lower($currency),
                'price' => $price,
            ];
        })
        ->sortBy('price')
        ->mapWithKeys(function ($price) {
            return [$price['currency'] => $price['price']];
        });

        $localizedCurrency = $this->currencies()->get(Site::current()->handle());
        $localizedPrice = $this->simpleCurrencyPrice();

        $localizedPrice = [Str::lower($localizedCurrency) => $localizedPrice];

        $prices = $prices->merge($localizedPrice);

        return json_encode($prices);
    }

    protected function currencies(): Collection
    {
        return collect(Currency::all())->map(function ($currency) {
            return $currency['code'];
        });
    }

    protected function url(): ?string
    {
        return Request::url();
    }

    protected function description(): ?string
    {
        return $this->data()->get('description');
    }

    protected function image(): ?string
    {
        if (! $this->entry()->root()->has('images')) {
            return null;
        }

        $imageUrl = $this->entry()->root()->augmentedValue('images')->value()[0]->url();

        if (config('snipcart.image.manipulation')) {
            return Image::manipulate($imageUrl, config('snipcart.image.preset'));
        }

        return $imageUrl;
    }

    protected function categories(): ?string
    {
        $categoryHandle = config('snipcart.taxonomies.categories');

        return $this->entry()->root()->augmentedValue($categoryHandle)->value()
            ->filter(function ($category) {
                return ! $category->get('hide_from_snipcart');
            })->map(function ($category) {
                return $category->title();
            })->implode('|');
    }

    protected function fileGuid(): ?string
    {
        return $this->data()->get('file_guid');
    }

    protected function metadata(): ?string
    {
        return json_encode($this->data()->get('metadata'));
    }

    protected function length(): ?string
    {
        return $this->lengthInCentimeters('length');
    }

    protected function width(): ?string
    {
        return $this->lengthInCentimeters('width');
    }

    protected function height(): ?string
    {
        return $this->lengthInCentimeters('height');
    }

    protected function weight(): ?string
    {
        $weight = $this->data()->get('weight');
        $weightInGrams = Converter::toGrams($weight, $this->weightUnit());

        return round($weightInGrams);
    }

    protected function shippable(): ?string
    {
        return Str::bool($this->data()->get('shippable'));
    }

    protected function taxable(): ?string
    {
        return Str::bool($this->data()->get('taxable'));
    }

    protected function hasTaxesIncluded(): ?string
    {
        return Str::bool($this->data()->get('has_taxes_included'));
    }

    protected function taxes(): ?string
    {
        if (! $this->data()->has('taxes')) {
            return null;
        }

        return implode('|', $this->data()->get('taxes'));
    }

    protected function stackable(): ?string
    {
        return $this->data()->get('stackable');
    }

    protected function quantity(): ?string
    {
        return $this->data()->get('quantity');
    }

    protected function quantityStep(): ?string
    {
        return $this->data()->get('quantity_step');
    }

    protected function minQuantity(): ?string
    {
        return $this->data()->get('min_quantity');
    }

    protected function maxQuantity(): ?string
    {
        return $this->data()->get('max_quantity');
    }

    protected function customFields(): Collection
    {
        $customFields = $this->variations()
            ->merge($this->checkboxes())
            ->merge($this->textFields())
            ->merge($this->readonlyFields());

        return $this->addCustomFieldIds($customFields);
    }

    protected function addCustomFieldIds(Collection $customFields): Collection
    {
        return $customFields->flatMap(function ($customField, $id) {
            $id++;

            return collect($customField)->mapWithKeys(function ($value, $key) use ($id) {
                return [
                    Str::replaceFirst('{key}', $id, $key) => $value,
                ];
            });
        });
    }

    protected function checkboxes(): Collection
    {
        $checkboxes = $this->data()->get('checkboxes');

        return collect($checkboxes)->map(function ($checkbox) {
            return [
                "custom{key}-name" => $checkbox['label'],
                "custom{key}-type" => 'checkbox',
            ];
        });
    }

    protected function textFields(): Collection
    {
        $textFields = $this->data()->get('text_fields');

        return collect($textFields)->map(function ($textField) {
            return [
                "custom{key}-name" => $textField['label'],
                "custom{key}-type" => $textField['size'] === 'large' ? 'textarea' : '',
                "custom{key}-value" => $textField['default'],
                "custom{key}-placeholder" => $textField['placeholder'],
                "custom{key}-required" => Str::bool($textField['required']),
            ];
        });
    }

    protected function readonlyFields(): Collection
    {
        $readonlyFields = $this->data()->get('readonly_fields');

        return collect($readonlyFields)->map(function ($readonlyField) {
            return [
                "custom{key}-name" => $readonlyField['label'],
                "custom{key}-type" => 'readonly',
                "custom{key}-value" => $readonlyField['text'],
            ];
        });
    }

    protected function variations(): Collection
    {
        $variations = $this->data()->get('variations');

        return collect($variations)->map(function ($variation, $key) {
            return [
                "custom{key}-name" => $variation['name'],
                "custom{key}-options" => $this->variationOptions($variation['options']),
                "custom{key}-value" => $this->variationValue($variation['options'], $key),
            ];
        });
    }

    protected function variationOptions(array $options): string
    {
        return collect($options)->map(function ($option) {
            $name = $option['name'];
            $price = $this->formatPriceModifier($option['price_modifier']);

            return (empty($price))
                ? $name
                : "{$name}[{$price}]";
        })->implode('|');
    }

    protected function variationValue(array $options, int $variationKey): ?string
    {
        if ($this->selectedVariant()->isEmpty()) {
            return null;
        }

        $value = collect($options)->filter(function ($option, $optionKey) use ($variationKey) {
            $selectedOptionKey = $this->selectedVariant()->filter(function ($selectedOptions) use ($variationKey, $optionKey) {
                return $selectedOptions['variation_key'] === $variationKey
                    && $selectedOptions['option_key'] === $optionKey ;
            })->pluck('option_key')->first();

            return $selectedOptionKey === $optionKey;
        })->pluck('name')->first();

        return $value;
    }

    protected function formatPriceModifier(?int $price): ?string
    {
        if (empty($price)) {
            return null;
        }

        $decimalPrice = Currency::from(Site::default())->formatDecimal($price);

        return (Str::startsWith($decimalPrice, '-'))
            ? $decimalPrice
            : "+$decimalPrice";
    }

    protected function lengthInCentimeters(string $key): string
    {
        $length = $this->data()->get($key);
        $lengthInCentimeters = Converter::toCentimeters($length, $this->lengthUnit());

        return round($lengthInCentimeters);
    }

    protected function lengthUnit(): string
    {
        $lengthUnit = $this->entry()->get('length_unit');

        if ($lengthUnit === null) {
            return Dimension::from(Site::default())
                ->type('length')
                ->short();
        }

        return $lengthUnit;
    }

    protected function weightUnit(): string
    {
        $weightUnit = $this->entry()->get('weight_unit');

        if ($weightUnit === null) {
            return Dimension::from(Site::default())
                ->type('weight')
                ->short();
        }

        return $weightUnit;
    }
}
