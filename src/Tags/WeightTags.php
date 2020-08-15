<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Dimension;
use Statamic\Facades\Site;
use Statamic\Tags\Tags;

class WeightTags extends Tags
{
    /**
     * The handle of the tag.
     *
     * @var string
     */
    protected static $handle = 'weight';

    /**
     * Return the unit's abbreviation.
     * {{ weight:short }}
     *
     * @return string
     */
    public function short(): string
    {
        return Dimension::from(Site::current())
            ->type('weight')
            ->short();
    }

    /**
     * Return the unit's singular name.
     * {{ weight:singular }}
     *
     * @return string
     */
    public function singular(): string
    {
        return Dimension::from(Site::current())
            ->type('weight')
            ->singular();
    }

    /**
     * Return the unit's plural name.
     * {{ weight:plural }}
     *
     * @return string
     */
    public function plural(): string
    {
        return Dimension::from(Site::current())
            ->type('weight')
            ->plural();
    }

    /**
     * Return the unit's singular/plural name.
     * {{ weight:name }}
     *
     * @return string
     */
    public function name(): string
    {
        return Dimension::from(Site::current())
            ->type('weight')
            ->name($this->context->value('weight'));
    }
}
