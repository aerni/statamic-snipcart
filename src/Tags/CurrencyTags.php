<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;
use Statamic\Tags\Tags;

class CurrencyTags extends Tags
{
    protected static $handle = 'currency';

    /**
     * Returns the currency code.
     * {{ currency:code }}
     */
    public function code(): string
    {
        return Currency::from(Site::current())->code();
    }

    /**
     * Returns the currency name.
     * {{ currency:name }}
     */
    public function name(): string
    {
        return Currency::from(Site::current())->name();
    }

    /**
     * Returns the currency symbol.
     * {{ currency:symbol }}
     */
    public function symbol(): string
    {
        return Currency::from(Site::current())->symbol();
    }
}
