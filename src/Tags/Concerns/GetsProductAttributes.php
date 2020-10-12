<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Aerni\Snipcart\Data\Product;

trait GetsProductAttributes
{
    /**
     * Get the Snipcart product attributes as HTML data-attribute string.
     *
     * @return string|null
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
     *
     * @return bool
     */
    protected function isProduct(): bool
    {
        if (! $this->context->has('is_snipcart_product')) {
            return false;
        }

        return true;
    }

    /**
     * Check if it's a product variant.
     *
     * @return bool
     */
    protected function isVariant(): bool
    {
        if (! is_array($this->context->get('variations'))) {
            return false;
        }

        return true;
    }
}
