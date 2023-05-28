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

        $product = (new Product($this->context->get('id')))
            ->params($this->params);

        if ($this->isVariant()) {
            $product->variant($this->context->get('variations'));
        }

        return $product->toHtmlDataString();
    }

    /**
     * Check if it's a Snipcart product.
     */
    protected function isProduct(): bool
    {
        return $this->context->raw('collection')->handle() === config('snipcart.products.collection');
    }

    /**
     * Check if it's a product variant.
     */
    protected function isVariant(): bool
    {
        if (! is_array($this->context->get('variations'))) {
            return false;
        }

        return true;
    }
}
