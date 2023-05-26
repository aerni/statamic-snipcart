<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Dimension;
use Statamic\Facades\Site;
use Statamic\Tags\Tags;

class LengthTags extends Tags
{
    protected static $handle = 'length';

    /**
     * Returns the unit's abbreviation.
     * {{ length:short }}
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
     */
    public function heightName(): string
    {
        return Dimension::from(Site::current())
            ->type('length')
            ->name($this->context->value('height'));
    }
}
