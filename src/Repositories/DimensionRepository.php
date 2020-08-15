<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\DimensionRepository as DimensionRepositoryContract;
use Aerni\Snipcart\Exceptions\UnsupportedDimensionTypeException;
use Aerni\Snipcart\Exceptions\UnsupportedDimensionUnitException;
use Aerni\Snipcart\Models\Dimension;
use Illuminate\Support\Facades\Config;
use Statamic\Sites\Site;

class DimensionRepository implements DimensionRepositoryContract
{
    /**
     * The site to get the dimension from.
     *
     * @var Site
     */
    protected $site;

    /**
     * The dimension (length/weight)
     *
     * @var string
     */
    protected $dimension;

    /**
     * Set the site property.
     *
     * @param Site $site
     */
    public function from(Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Set the dimension property
     *
     * @param string $dimension
     * @return self
     */
    public function type(string $dimension): self
    {
        if ($dimension !== 'length' && $dimension !== 'weight') {
            throw new UnsupportedDimensionTypeException($dimension);
        }

        $this->dimension = $dimension;

        return $this;
    }

    /**
     * Get an array of the unit's data.
     *
     * @return array
     */
    public function all(): array
    {
        $unitSetting = collect(Config::get('snipcart.sites'))
            ->get($this->site->handle())[$this->dimension];

        $unit = Dimension::where('dimension', $this->dimension)->where('short', $unitSetting)->first();

        if (is_null($unit)) {
            throw new UnsupportedDimensionUnitException($this->site->handle(), $this->dimension, $unitSetting);
        }

        return $unit->toArray();
    }

    /**
     * Get a unit value by key.
     *
     * @return string
     */
    public function get(string $key): string
    {
        return $this->all()[$key];
    }

    /**
     * Get the unit's abbreviation.
     *
     * @return string
     */
    public function short(): string
    {
        return $this->get('short');
    }

    /**
     * Get the unit's singular name.
     *
     * @return string
     */
    public function singular(): string
    {
        return $this->get('singular');
    }

    /**
     * Get the unit's plural name.
     *
     * @return string
     */
    public function plural(): string
    {
        return $this->get('plural');
    }

    /**
     * Get the unit's singular/plural name.
     *
     * @param string|null $value
     * @return string
     */
    public function name(?string $value): string
    {
        if ($value > 1) {
            return $this->plural();
        }

        return $this->singular();
    }

    /**
     * Parse the value.
     *
     * @param string|null $value
     * @return string|null
     */
    public function parse(?string $value)
    {
        return $value;
    }
}
