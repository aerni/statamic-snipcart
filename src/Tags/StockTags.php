<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\ProductApi;
use Statamic\Tags\Tags;

class StockTags extends Tags
{
    protected static $handle = 'stock';

    public function index(): ?string
    {
        return ProductApi::find($this->context->get('sku'))
            ->variant($this->context->get('options'))
            ->stock();
    }
}
