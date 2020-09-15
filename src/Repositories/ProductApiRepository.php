<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\SnipcartApi\Facades\SnipcartApi;

class ProductApiRepository
{
    public function stock(string $sku): string
    {
        $product = SnipcartApi::get()
            ->product($sku)
            ->send();

        return $product->get('stock') ?? '';
    }
}
