<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Aerni\Snipcart\Facades\Product;
use Aerni\Snipcart\Support\Validator;
use Illuminate\Support\Collection;
use Statamic\Facades\Entry;

trait GetsProductAttributes
{
    /**
     * Get all the Snipcart attributes as an HTML-ready string.
     *
     * @return string
     */
    protected function dataAttributes(): string
    {
        return $this->mergedAttributes()->map(function ($value, $key) {
            return "data-item-{$key}='{$value}'";
        })->implode(' ');
    }

    /**
     * Get the merged and valid Snipcart attributes.
     *
     * @return Collection
     */
    protected function mergedAttributes(): Collection
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
            $selectedVariantOptions = $this->context->get('options');
            $entry = Entry::find($this->context->get('id'));

            return Product::selectedVariantOptions($selectedVariantOptions)->processAttributes($entry);
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
