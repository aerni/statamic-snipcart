<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Aerni\Snipcart\Data\Product;
use Statamic\Contracts\Entries\Entry;

trait GetsProductAttributes
{
    /**
     * Get the Snipcart product attributes as HTML data-attribute string.
     */
    protected function productAttributes(): ?string
    {
        if (! $this->isProduct()) {
            return null;
        }

        return (new Product($this->entry()))
            ->params($this->params)
            ->toHtmlDataString();
    }

    /**
     * Gets the product's entry.
     */
    protected function entry(): Entry
    {
        return $this->context->get('is_entry')->augmentable();
    }

    /**
     * Check if we're dealing with a Snipcart product.
     */
    protected function isProduct(): bool
    {
        return $this->context->raw('collection')->handle() === config('snipcart.products.collection');
    }
}
