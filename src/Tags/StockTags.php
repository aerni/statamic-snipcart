<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\ProductApi;
use Statamic\Tags\Tags;

class StockTags extends Tags
{
    protected static $handle = 'stock';

    public function index(): ?string
    {
        $product = ProductApi::find($this->context->get('sku'));

        if ($product === null) {
            return null;
        }

        return $product
            ->variant($this->context->get('variations'))
            ->stock();
    }
}
