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

    protected function id(): string
    {
        return $this->entry()->sku;
    }

    protected function price(): ?string
    {
        if (count($this->currencies()->unique()) === 1) {
            return $this->simpleCurrencyPrice();
        }

        return $this->multiCurrencyPrices();
    }

    protected function simpleCurrencyPrice(): string
    {
        return Currency::from(Site::current())
            ->formatDecimal($this->data()->get('price'));
    }

    protected function multiCurrencyPrices(): string
    {
        $prices = $this->entries()->map(function ($entry) {
            $currency = $this->currencies()->get($entry->locale());

            $price = Currency::from($entry->site())
                ->formatDecimal($entry->get('price'));

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
        return collect(Currency::all())
            ->map(fn ($currency) => $currency['code']);
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
        if (empty($this->entry()->root()->get('images'))) {
            return null;
        }

        $imageUrl = $this->entry()->root()->images[0]->url();

        if (config('snipcart.image.manipulation')) {
            return Image::manipulate($imageUrl, config('snipcart.image.preset'));
        }

        return $imageUrl;
    }

    protected function categories(): ?string
    {
        $categoryHandle = config('snipcart.categories.taxonomy');

        if (empty($this->entry()->root()->get($categoryHandle))) {
            return null;
        }

        return $this->entry()->root()->$categoryHandle
            ->filter(fn ($category) => ! $category->get('hide_from_snipcart'))
            ->map(fn ($category) => $category->title())
            ->implode('|');
    }

    protected function fileGuid(): ?string
    {
        return $this->data()->get('file_guid');
    }

    protected function metadata(): ?string
    {
        if (! $this->data()->has('metadata')) {
            return null;
        }

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
        if (! $weight = $this->data()->get('weight')) {
            return null;
        }

        return round(Converter::toGrams($weight, $this->weightUnit()));
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

        return collect($checkboxes)->map(fn ($checkbox) => [
            'custom{key}-name' => $checkbox['label'],
            'custom{key}-options' => $this->checkboxOptions($checkbox),
            'custom{key}-type' => 'checkbox',
            'custom{key}-value' => Str::bool($checkbox['checked'] ?? false),
        ]);
    }

    protected function textFields(): Collection
    {
        $textFields = $this->data()->get('text_fields');

        return collect($textFields)->map(function ($textField) {
            return [
                'custom{key}-name' => $textField['label'],
                'custom{key}-type' => $textField['size'] === 'large' ? 'textarea' : '',
                'custom{key}-value' => $textField['default'],
                'custom{key}-placeholder' => $textField['placeholder'],
                'custom{key}-required' => Str::bool($textField['required']),
            ];
        });
    }

    protected function readonlyFields(): Collection
    {
        $readonlyFields = $this->data()->get('readonly_fields');

        return collect($readonlyFields)->map(function ($readonlyField) {
            return [
                'custom{key}-name' => $readonlyField['label'],
                'custom{key}-type' => 'readonly',
                'custom{key}-value' => $readonlyField['text'],
            ];
        });
    }

    protected function variations(): Collection
    {
        $variations = $this->data()->get('variations');

        return collect($variations)->map(function ($variation, $key) {
            return [
                'custom{key}-name' => $variation['name'],
                'custom{key}-options' => $this->variationOptions($variation['options']),
                'custom{key}-value' => $this->variationValue($variation['options'], $key),
            ];
        });
    }

    protected function checkboxOptions(array $checkbox): ?string
    {
        if (! $price = $this->formatPriceModifier($checkbox['price_modifier'])) {
            return null;
        }

        return "true[{$price}]|false";
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
        /**
         * We only want to pre-select a variation in the Snipcart checkout if we're inside a variant loop.
         */
        if ($this->variant()->isEmpty()) {
            return null;
        }

        $value = collect($options)->filter(function ($option, $optionKey) use ($variationKey) {
            $selectedOptionKey = $this->variant()
                ->replaceRecursive($this->variantKeys())
                ->filter(function ($selectedOptions) use ($variationKey, $optionKey) {
                    return $selectedOptions['variation_key'] === $variationKey
                        && $selectedOptions['option_key'] === $optionKey;
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

    protected function lengthInCentimeters(string $key): ?string
    {
        if (! $length = $this->data()->get($key)) {
            return null;
        }

        return round(Converter::toCentimeters($length, $this->lengthUnit()));
    }

    protected function lengthUnit(): string
    {
        return Dimension::from($this->entry()->root()->site())
            ->type('length')
            ->short();
    }

    protected function weightUnit(): string
    {
        return Dimension::from($this->entry()->root()->site())
            ->type('weight')
            ->short();
    }
}
