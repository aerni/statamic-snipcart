<?php

namespace Aerni\Snipcart\Tags\Concerns;

use Aerni\Snipcart\Facades\Product;
use Aerni\Snipcart\Support\Validator;
use Illuminate\Support\Collection;
use Statamic\Facades\Entry;

trait GetsProductAttributes
{
    use TagGuards;

    /**
     * Get all the Snipcart attributes as an HTML-ready string.
     *
     * @return string
     */
    protected function dataAttributes(): string
    {
        return $this->validAttributes()->map(function ($value, $key) {
            return "data-item-{$key}='{$value}'";
        })->implode(' ');
    }

    /**
     * Get the merged and valid Snipcart attributes.
     *
     * @return Collection
     */
    protected function validAttributes(): Collection
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
            $entry = Entry::find($this->context->get('id'));

            if ($this->isProductVariant()) {
                return Product::selectedVariantOptions($this->context->get('options'))
                    ->processAttributes($entry);
            };

            return Product::processAttributes($entry);
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
}
