<?php

namespace Aerni\Snipcart;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
     *
     * @param Collection $attributes
     * @return Collection
     */
    public static function validateAttributes(Collection $attributes): Collection
    {
        if (Self::hasRequiredAttributes($attributes)) {
            return $attributes;
        }

        throw new Exception("Please make sure that your products include the required attributes: [name], [id], [price], [url]");
    }

    /**
     * Filter invalid attributes.
     *
     * @param Collection $attributes
     * @return Collection
     */
    public static function onlyValidAttributes(Collection $attributes): Collection
    {
        return $attributes->map(function ($item, $key) {
            if (Self::isValidAttribute($key) && Self::isValidItem($item)) {
                return trim($item);
            }
        })->filter();
    }

    /**
     * Check if the key is a valid Snipcart attribute.
     *
     * @param string $key
     * @return bool
     */
    protected static function isValidAttribute(string $key): bool
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
     * Check if the item is a valid Snipcart attribute value.
     *
     * @param mixed $item
     * @return bool
     */
    protected static function isValidItem($item): bool
    {
        if (is_array($item)) {
            return false;
        }

        if (empty($item)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the attributes include Snipcart's mandatory product attributes.
     *
     * @param Collection $attributes
     * @return bool
     */
    protected static function hasRequiredAttributes(Collection $attributes): bool
    {
        if ($attributes->has(Self::$requiredAttributes)) {
            return true;
        };

        return false;
    }
}
