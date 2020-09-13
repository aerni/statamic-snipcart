<?php

namespace Aerni\Snipcart\Modifiers;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;
use Statamic\Modifiers\Modifier;

class Total extends Modifier
{
    /**
     * Calculate the total of the product price and variant price modifier.
     *
     * @param mixed $value
     * @param array $params
     * @param array $context
     * @return mixed
     */
    public function index($value, $params, $context): string
    {
        $total = collect($context)->only(['price', 'price_modifier'])
            ->map(function ($price) {
                return $price->raw();
            })->sum();

        return Currency::from(Site::current())->formatCurrency($total);
    }
}
