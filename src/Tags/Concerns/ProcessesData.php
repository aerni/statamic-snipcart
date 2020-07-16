<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Aerni\Snipcart\Attributes;
use Aerni\Snipcart\Product;
use Aerni\Snipcart\Validator;
use Illuminate\Support\Collection;

trait ProcessesData
{
    use Attributes;

    /**
     * Get all the Snipcart attributes as an HTML-ready string.
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
     * Get valid Snipcart attributes.
     *
     * @return Collection
     */
    protected function attributes(): Collection
    {
        $productAttributes = $this->productAttributes();
        $tagAttributes = $this->tagAttributes();
        $mergedAttributes = $productAttributes->merge($tagAttributes);

        return Validator::validateAttributes($mergedAttributes);
    }

    /**
     * Get the Snipcart attributes from the product entry.
     *
     * @return Collection
     */
    protected function productAttributes(): Collection
    {
        if ($this->isProduct()) {
            $productAttributes = (new Product($this->context))->attributes();

            return $this->onlyValidAttributes($productAttributes);
        }

        return collect();
    }

    /**
     * Get the Snipcart attributes from the tag.
     *
     * @return Collection
     */
    protected function tagAttributes(): Collection
    {
        return $this->onlyValidAttributes($this->params);
    }

    /**
     * Only get valid attributes.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function onlyValidAttributes(Collection $attributes): Collection
    {
        return $attributes->filter(function ($item, $key) {
            if (Validator::isValidAttribute($key)) {
                return $item;
            }
        });
    }

    /**
     * Return true if it's a Snipcart product.
     *
     * @return bool
     */
    protected function isProduct(): bool
    {
        if ($this->context->has('is_snipcart_product')) {
            return true;
        }

        return false;
    }
}
