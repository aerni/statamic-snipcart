<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Dimension;
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
     * An alias of the tag handle.
     *
     * @var array
     */
    protected static $aliases = ['len'];

    /**
     * Return the length unit's abbreviation.
     * {{ length:short }}
     *
     * @return string
     */
    public function short(): string
    {
        return Dimension::type('length')->short();
    }

    /**
     * Return the length unit's singular name.
     * {{ length:singular }}
     *
     * @return string
     */
    public function singular(): string
    {
        return Dimension::type('length')->singular();
    }

    /**
     * Return the length unit's plural name.
     * {{ length:plural }}
     *
     * @return string
     */
    public function plural(): string
    {
        return Dimension::type('length')->plural();
    }

    /**
     * Return the length unit's length name as singular or plural.
     * {{ length:lengthName }}
     *
     * @return string
     */
    public function lengthName(): string
    {
        return Dimension::type('length')->name($this->context->value('length'));
    }

    /**
     * Return the length unit's width name as singular or plural.
     * {{ length:widthName }}
     *
     * @return string
     */
    public function widthName(): string
    {
        return Dimension::type('length')->name($this->context->value('width'));
    }

    /**
     * Return the length unit's height name as singular or plural.
     * {{ length:heightName }}
     *
     * @return string
     */
    public function heightName(): string
    {
        return Dimension::type('length')->name($this->context->value('height'));
    }
}
