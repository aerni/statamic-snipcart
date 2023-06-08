<?php

namespace Aerni\Snipcart\Support;

use Aerni\Snipcart\Exceptions\UnsupportedAttributeException;
use Illuminate\Support\Collection;
use Statamic\Support\Str;

class Validator
{
    /**
     * All the mandatory Snipcart product attributes.
     *
     * @var array
     */
    protected static $requiredAttributes = ['name', 'id', 'price', 'url'];

    /**
     * All the optional Snipcart product attributes.
     *
     * @var array
     */
    protected static $optionalAttributes = [
        'description', 'image', 'categories', 'metadata', 'weight', 'length', 'height', 'width', 'quantity', 'max-quantity', 'min-quantity', 'stackable', 'quantity-step', 'shippable', 'taxable', 'taxes', 'has-taxes-included', 'file-guid',
    ];

    /**
     * Validate the attributes.
     */
    public static function validateAttributes(Collection $attributes): Collection
    {
        if (self::hasRequiredAttributes($attributes)) {
            return $attributes;
        }

        throw new UnsupportedAttributeException();
    }

    /**
     * Filter invalid attributes.
     */
    public static function onlyValidAttributes(Collection $attributes): Collection
    {
        return $attributes->map(function ($value, $key) {
            if (self::isValidAttributeKey($key) && self::isValidAttributeValue($value)) {
                if (is_bool($value)) {
                    return Str::bool($value);
                }

                return $value;
            }
        })->filter();
    }

    /**
     * Check if the key is a valid Snipcart attribute key.
     */
    protected static function isValidAttributeKey(string $key): bool
    {
        if (in_array($key, self::$requiredAttributes)) {
            return true;
        }

        if (in_array($key, self::$optionalAttributes)) {
            return true;
        }

        if (Str::startsWith($key, 'custom') && is_numeric(Str::between($key, 'custom', '-'))) {
            return true;
        }

        return false;
    }

    /**
     * Check if the value is a valid Snipcart attribute value.
     */
    protected static function isValidAttributeValue(mixed $value): bool
    {
        if (is_array($value)) {
            return false;
        }

        if (is_null($value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the attributes include all mandatory product attributes.
     */
    protected static function hasRequiredAttributes(Collection $attributes): bool
    {
        if ($attributes->has(self::$requiredAttributes)) {
            return true;
        }

        return false;
    }
}
