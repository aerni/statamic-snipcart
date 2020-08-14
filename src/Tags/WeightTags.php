<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Dimension;
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
     * Return the weight unit's abbreviation.
     * {{ weight:short }}
     *
     * @return string
     */
    public function short(): string
    {
        return Dimension::type('weight')->short();
    }

    /**
     * Return the weight unit's singular name.
     * {{ weight:singular }}
     *
     * @return string
     */
    public function singular(): string
    {
        return Dimension::type('weight')->singular();
    }

    /**
     * Return the weight unit's plural name.
     * {{ weight:plural }}
     *
     * @return string
     */
    public function plural(): string
    {
        return Dimension::type('weight')->plural();
    }

    /**
     * Return the weight unit's name as singular or plural.
     * {{ weight:name }}
     *
     * @return string
     */
    public function name(): string
    {
        return Dimension::type('weight')->name($this->context->value('weight'));
    }
}
