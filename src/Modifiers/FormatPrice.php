<?php

namespace Aerni\Snipcart\Modifiers;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;
use Statamic\Modifiers\Modifier;

class FormatPrice extends Modifier
{
    /**
     * Format an integer price to a nice string with currency.
     *
     * @param int $value
     * @return string
     */
    public function index($value): string
    {
        return Currency::from(Site::current())->formatCurrency($value);
    }
}
