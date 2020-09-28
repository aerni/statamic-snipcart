<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Aerni\Snipcart\Data\Product;

trait GetsProductAttributes
{
    /**
     * Get all the Snipcart product attributes.
     *
     * @return string
     */
    protected function productAttributes(): ?string
    {
        if (! $this->isProduct()) {
            return null;
        }

        $product = (new Product($this->context->get('id')))
            ->params($this->params);

        if ($this->isProductVariant()) {
            $product->selectedVariantOptions($this->context->get('options'));
        };

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
     * Check if it's a Snipcart product variant.
     *
     * @return bool
     */
    protected function isProductVariant(): bool
    {
        if (! $this->isProduct()) {
            return false;
        }

        if (! $this->context->has('options')) {
            return false;
        }

        if (! is_array($this->context->get('options'))) {
            return false;
        }

        return true;
    }
}
