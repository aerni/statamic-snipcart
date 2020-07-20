<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Aerni\Snipcart\Facades\Product;
use Aerni\Snipcart\Validator;
use Illuminate\Support\Collection;

trait ProcessesData
{
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
            return Product::find($this->context->get('id'))->attributes();
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
        return Validator::onlyValidAttributes($this->params);
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
