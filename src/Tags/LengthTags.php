<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Dimension;
use Statamic\Facades\Site;
use Statamic\Tags\Tags;

class LengthTags extends Tags
{
    /**
     * The handle of the tag.
     *
     * @var string
     */
    protected static $handle = 'length';

    /**
     * Returns the unit's abbreviation.
     * {{ length:short }}
     *
     * @return string
     */
    public function short(): string
    {
        return Dimension::from(Site::current())
            ->type('length')
            ->short();
    }

    /**
     * Returns the unit's singular name.
     * {{ length:singular }}
     *
     * @return string
     */
    public function singular(): string
    {
        return Dimension::from(Site::current())
            ->type('length')
            ->singular();
    }

    /**
     * Returns the unit's plural name.
     * {{ length:plural }}
     *
     * @return string
     */
    public function plural(): string
    {
        return Dimension::from(Site::current())
            ->type('length')
            ->plural();
    }

    /**
     * Returns the unit's length singular/plural name.
     * {{ length:lengthName }}
     *
     * @return string
     */
    public function lengthName(): string
    {
        return Dimension::from(Site::current())
            ->type('length')
            ->name($this->context->value('length'));
    }

    /**
     * Returns the unit's width singular/plural name.
     * {{ length:widthName }}
     *
     * @return string
     */
    public function widthName(): string
    {
        return Dimension::from(Site::current())
            ->type('length')
            ->name($this->context->value('width'));
    }

    /**
     * Returns the unit's height singular/plural name.
     * {{ length:heightName }}
     *
     * @return string
     */
    public function heightName(): string
    {
        return Dimension::from(Site::current())
            ->type('length')
            ->name($this->context->value('height'));
    }
}
