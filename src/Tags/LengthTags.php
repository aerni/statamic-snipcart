<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Length;
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
     * Return the default length unit's abbreviation.
     * {{ length:short }}
     *
     * @return string
     */
    public function short(): string
    {
        return Length::short();
    }

    /**
     * Return the default length unit's singular name.
     * {{ length:singular }}
     *
     * @return string
     */
    public function singular(): string
    {
        return Length::singular();
    }

    /**
     * Return the default length unit's plural name.
     * {{ length:plural }}
     *
     * @return string
     */
    public function plural(): string
    {
        return Length::plural();
    }

    /**
     * Return the default length unit's name as singular or plural.
     * {{ length:lengthName }}
     *
     * @return string
     */
    public function lengthName(): string
    {
        return Length::name($this->context->value('length'));
    }

    /**
     * Return the default length unit's name as singular or plural.
     * {{ length:widthName }}
     *
     * @return string
     */
    public function widthName(): string
    {
        return Length::name($this->context->value('width'));
    }

    /**
     * Return the default length unit's name as singular or plural.
     * {{ length:heightName }}
     *
     * @return string
     */
    public function heightName(): string
    {
        return Length::name($this->context->value('height'));
    }
}
