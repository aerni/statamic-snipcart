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
     * Return the default length unit.
     * {{ length }}
     *
     * @return array
     */
    public function index(): array
    {
        return length::default();
    }

    /**
     * Return the default length unit's abbreviation.
     * {{ length:abbr }}
     *
     * @return string
     */
    public function abbr(): string
    {
        return Length::abbr();
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
}
