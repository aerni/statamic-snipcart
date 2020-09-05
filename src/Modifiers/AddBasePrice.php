<?php

namespace Aerni\Snipcart\Modifiers;

use Statamic\Facades\Site;
use Statamic\Modifiers\Modifier;
use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Entry;

class AddBasePrice extends Modifier
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
        $basePrice = Entry::find($context['id'])->augmentedValue('price')->raw();
        $variantPrice = Currency::from(Site::current())->parseCurrency($value);
        $totalPrice = $basePrice + $variantPrice;

        return Currency::from(Site::current())->formatCurrency($totalPrice);
    }
}
