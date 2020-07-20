<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Weight;
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
     * An alias of the tag handle.
     *
     * @var array
     */
    protected static $aliases = ['wgt'];

    /**
     * Return the default weight unit's abbreviation.
     * {{ weight:short }}
     *
     * @return string
     */
    public function short(): string
    {
        return Weight::short();
    }

    /**
     * Return the default weight unit's singular name.
     * {{ weight:singular }}
     *
     * @return string
     */
    public function singular(): string
    {
        return Weight::singular();
    }

    /**
     * Return the default weight unit's plural name.
     * {{ weight:plural }}
     *
     * @return string
     */
    public function plural(): string
    {
        return Weight::plural();
    }

    /**
     * Return the default weight unit's name as singular or plural.
     * {{ weight:name }}
     *
     * @return string
     */
    public function name(): string
    {
        return Weight::name($this->context->value('weight'));
    }
}
