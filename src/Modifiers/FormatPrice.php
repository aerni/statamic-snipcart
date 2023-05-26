<?php

namespace Aerni\Snipcart\Modifiers;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;
use Statamic\Modifiers\Modifier;

class FormatPrice extends Modifier
{
    /**
     * Format an integer price to a nice string with currency.
     */
    public function index(int $value): string
    {
        return Currency::from(Site::current())->formatCurrency($value);
    }
}
