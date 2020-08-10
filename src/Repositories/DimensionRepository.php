<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\DimensionRepository as DimensionRepositoryContract;
use Aerni\Snipcart\Exceptions\UnsupportedDimensionTypeException;
use Aerni\Snipcart\Exceptions\UnsupportedDimensionUnitException;
use Aerni\Snipcart\Models\Dimension;
use Illuminate\Support\Str;

class DimensionRepository implements DimensionRepositoryContract
{
    /**
     * The dimension type
     *
     * @var string
     */
    protected $type;

    /**
     * The length or width unit.
     *
     * @var string
     */
    protected $unit;

    /**
     * Set the dimension type and unit.
     *
     * @param string $type
     * @return self
     */
    public function type(string $type): self
    {
        $this->type = $type;

        if ($type === 'length') {
            $this->unit = config('snipcart.length');
            return $this;
        }
        
        if ($type === 'weight') {
            $this->unit = config('snipcart.weight');
            return $this;
        }

        throw new UnsupportedDimensionTypeException();
    }

    /**
     * Get an array of the unit's information.
     *
     * @return object
     */
    public function all(): array
    {
        $unit = Dimension::firstWhere('short', $this->unit);
        
        if (is_null($unit)) {
            throw new UnsupportedDimensionUnitException($this->type, $this->unit);
        }
        
        return $unit->only(['short', 'singular', 'plural']);
    }

    /**
     * Get the unit's abbreviation.
     *
     * @return string
     */
    public function short(): string
    {
        return $this->all()['short'];
    }

    /**
     * Get the unit's singular name.
     *
     * @return string
     */
    public function singular(): string
    {
        return $this->all()['singular'];
    }

    /**
     * Get the unit's plural name.
     *
     * @return string
     */
    public function plural(): string
    {
        return $this->all()['plural'];
    }

    /**
     * Get the unit's name as singular or plural.
     *
     * @param mixed $value
     * @return string
     */
    public function name($value): string
    {
        if (is_null($value)) {
            $this->singular();
        }

        if ($value <= 1) {
            return $this->singular();
        }
        
        return $this->plural();
    }

    /**
     * Parse the value.
     *
     * @param mixed $value
     * @return mixed
     */
    public function parse($value)
    {
        if (Str::startsWith($value, '-')) {
            return null;
        }

        if ($value === '0') {
            return null;
        }

        return $value;
    }
}
