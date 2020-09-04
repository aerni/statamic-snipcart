<?php

namespace Aerni\Snipcart\Modifiers;

use Statamic\Facades\Site;
use Statamic\Modifiers\Modifier;
use Aerni\Snipcart\Facades\Currency;

class FormatPrice extends Modifier
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
        return Currency::from(Site::current())->formatCurrency($value);
    }
}
