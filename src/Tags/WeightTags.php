<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Dimension;
use Statamic\Facades\Site;
use Statamic\Tags\Tags;

class WeightTags extends Tags
{
    protected static $handle = 'weight';

    /**
     * Returns the unit's abbreviation.
     * {{ weight:short }}
     */
    public function short(): string
    {
        return Dimension::from(Site::current())
            ->type('weight')
            ->short();
    }

    /**
     * Returns the unit's singular name.
     * {{ weight:singular }}
     */
    public function singular(): string
    {
        return Dimension::from(Site::current())
            ->type('weight')
            ->singular();
    }

    /**
     * Returns the unit's plural name.
     * {{ weight:plural }}
     */
    public function plural(): string
    {
        return Dimension::from(Site::current())
            ->type('weight')
            ->plural();
    }

    /**
     * Returns the unit's singular/plural name.
     * {{ weight:name }}
     */
    public function name(): string
    {
        return Dimension::from(Site::current())
            ->type('weight')
            ->name($this->context->raw('weight'));
    }
}
