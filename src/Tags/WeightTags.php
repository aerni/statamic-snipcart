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
     * Return the default weight unit.
     * {{ weight }}
     *
     * @return array
     */
    public function index(): array
    {
        return Weight::default();
    }

    /**
     * Return the default weight unit's abbreviation.
     * {{ weight:abbr }}
     *
     * @return string
     */
    public function abbr(): string
    {
        return Weight::abbr();
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
}
