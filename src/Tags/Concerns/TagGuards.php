<?php

namespace Aerni\Snipcart\Tags\Concerns;

trait TagGuards
{
    /**
     * Return true if it's a Snipcart product.
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
     * Returns true if it's a product variant.
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
