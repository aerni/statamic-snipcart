<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\ProductApi;
use Statamic\Tags\Tags;

class StockTags extends Tags
{
    /**
     * The handle of the tag.
     *
     * @var string
     */
    protected static $handle = 'stock';

    /**
     * Returns the stock of a product.
     *
     * @return string
     */
    public function index(): string
    {
        return ProductApi::stock($this->context->get('sku'));
    }
}
