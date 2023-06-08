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
    protected function name(): string
    {
        return $this->data()->get('title');
    }

    protected function id(): string
    {
        return $this->entry()->sku;
    }

    protected function price(): string
    {
        return $this->currencies()->unique()->count() === 1
            ? $this->simpleCurrencyPrice()
            : $this->multiCurrencyPrices();
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
        $productTaxonomies = collect(config('snipcart.products'))
            ->where('collection', $this->entry()->collection()->handle())
            ->flatMap(fn ($product) => $product['taxonomies'])
            ->filter(fn ($taxonomy) => $this->entry()->collection()->taxonomies()->map->handle()->contains($taxonomy)); // Only keep the taxonomies that are configured for the collection.

        if ($productTaxonomies->isEmpty()) {
            return null;
        }

        return $productTaxonomies->flatMap(function ($taxonomy) {
            return $this->entry()->root()->$taxonomy
                ->filter(fn ($term) => ! $term->get('hide_from_snipcart'))
                ->map(fn ($term) => $term->title());
        })->implode('|');
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
        return collect($this->data()->get('custom_fields'))
            ->filter(fn ($field) => $field['enabled'])
            ->flatMap(fn ($field, $key) => $this->{$field['type']}($field, $key + 1));
    }

    protected function checkbox(array $field, int $key): array
    {
        return [
            "custom{$key}-name" => $field['name'],
            "custom{$key}-options" => $this->checkboxOptions($field),
            "custom{$key}-type" => $field['hidden'] ? 'hidden' : 'checkbox',
            "custom{$key}-value" => Str::bool($field['checked'] ?? false),
        ];
    }

    protected function checkboxOptions(array $field): ?string
    {
        if (empty($field['price_modifier'])) {
            return null;
        }

        $price = $this->formatPriceModifier($field['price_modifier']);

        return "true[{$price}]|false";
    }

    protected function dropdown(array $field, int $key): array
    {
        return [
            "custom{$key}-name" => $field['name'],
            "custom{$key}-options" => $this->dropdownOptions($field),
            "custom{$key}-type" => $field['hidden'] ? 'hidden' : null,
            "custom{$key}-value" => $this->dropdownValue($field),
        ];
    }

    protected function dropdownOptions(array $field): string
    {
        return collect($field['options'])->map(function ($option) {
            if (empty($option['price_modifier'])) {
                return $option['name'];
            }

            $price = $this->formatPriceModifier($option['price_modifier']);

            return "{$option['name']}[{$price}]";
        })->implode('|');
    }

    protected function dropdownValue(array $field): ?string
    {
        $option = collect($field['options'])->firstWhere('default', true);

        return $option['name'] ?? null;
    }

    protected function readonly(array $field, int $key): array
    {
        return [
            "custom{$key}-name" => $field['name'],
            "custom{$key}-options" => $this->readonlyOptions($field),
            "custom{$key}-type" => $field['hidden'] ? 'hidden' : 'readonly',
            "custom{$key}-value" => $field['text'],
        ];
    }

    protected function readonlyOptions(array $field): ?string
    {
        if (empty($field['price_modifier'])) {
            return null;
        }

        $price = $this->formatPriceModifier($field['price_modifier']);

        return "{$field['text']}[{$price}]";
    }

    protected function text(array $field, int $key): array
    {
        return [
            "custom{$key}-name" => $field['name'],
            "custom{$key}-type" => $field['size'] === 'large' ? 'textarea' : null,
            "custom{$key}-value" => $field['default'] ?? null,
            "custom{$key}-placeholder" => $field['placeholder'] ?? null,
            "custom{$key}-required" => Str::bool($field['required']),
        ];
    }

    protected function formatPriceModifier(int $price): string
    {
        $decimalPrice = Currency::from(Site::default())->formatDecimal($price);

        return Str::startsWith($decimalPrice, '-')
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
