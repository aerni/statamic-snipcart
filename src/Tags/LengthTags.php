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
     * Return the unit's abbreviation.
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
     * Return the unit's singular name.
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
     * Return the unit's plural name.
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
     * Return the unit's length singular/plural name.
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
     * Return the unit's width singular/plural name.
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
     * Return the unit's height singular/plural name.
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
