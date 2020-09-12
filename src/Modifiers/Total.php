<?php

namespace Aerni\Snipcart\Modifiers;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;
use Statamic\Modifiers\Modifier;

class Total extends Modifier
{
    /**
     * Modify a value.
     *
     * @param mixed  $value    The value to be modified
     * @param array  $params   Any parameters used in the modifier
     * @param array  $context  Contextual values
     * @return mixed
     */
    public function index($value, $params, $context)
    {
        $total = collect($context)->only(['price', 'price_modifier'])
            ->map(function ($price) {
                return $price->raw();
            })->sum();

        return Currency::from(Site::current())->formatCurrency($total);
    }
}
