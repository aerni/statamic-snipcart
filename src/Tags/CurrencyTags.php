<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Tags\Tags;
use Statamic\Facades\Site;

class CurrencyTags extends Tags
{
    /**
     * The handle of the tag.
     *
     * @var string
     */
    protected static $handle = 'currency';

    /**
     * An alias of the tag handle.
     *
     * @var array
     */
    protected static $aliases = ['cy'];

    /**
     * Return the currency code.
     * {{ currency:code }}
     *
     * @return string
     */
    public function code(): string
    {
        return Currency::code(Site::current());
    }

    /**
     * Return the currency name.
     * {{ currency:name }}
     *
     * @return string
     */
    public function name(): string
    {
        return Currency::name(Site::current());
    }

    /**
     * Return the currency symbol.
     * {{ currency:symbol }}
     *
     * @return string
     */
    public function symbol(): string
    {
        return Currency::symbol(Site::current());
    }
}
