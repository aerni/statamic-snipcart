<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Aerni\Snipcart\Data\Product;

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

        return (new Product($this->context->get('id')))
            ->params($this->params)
            ->selectedVariant($this->context->get('options'))
            ->toHtmlDataString();
    }

    /**
     * Check if it's a Snipcart product.
     */
    protected function isProduct(): bool
    {
        if (! $this->context->has('is_snipcart_product')) {
            return false;
        }

        return true;
    }
}
