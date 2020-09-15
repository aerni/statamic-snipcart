<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;
use Statamic\Tags\Tags;

class CurrencyTags extends Tags
{
    /**
     * The handle of the tag.
     *
     * @var string
     */
    protected static $handle = 'currency';

    /**
     * Returns the currency code.
     * {{ currency:code }}
     *
     * @return string
     */
    public function code(): string
    {
        return Currency::from(Site::current())->code();
    }

    /**
     * Returns the currency name.
     * {{ currency:name }}
     *
     * @return string
     */
    public function name(): string
    {
        return Currency::from(Site::current())->name();
    }

    /**
     * Returns the currency symbol.
     * {{ currency:symbol }}
     *
     * @return string
     */
    public function symbol(): string
    {
        return Currency::from(Site::current())->symbol();
    }
}
