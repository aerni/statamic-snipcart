<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\WeightRepository as WeightRepositoryContract;
use Aerni\Snipcart\Models\Weight;
use Exception;
use Illuminate\Support\Str;

class WeightRepository implements WeightRepositoryContract
{
    /**
     * The weight unit from the config.
     *
     * @var string
     */
    protected $unit;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->unit = config('snipcart.weight');
    }

    /**
     * Get an array of the weight unit.
     *
     * @return object
     */
    public function default(): array
    {
        $unit = Weight::firstWhere('short', $this->unit);
        
        if (! is_null($unit)) {
            return $unit->only(['short', 'singular', 'plural']);
        }

        throw new Exception('This weight unit is not supported. Please make sure to set a supported unit in your config.');
    }

    /**
     * Get the default weight unit's abbreviation.
     *
     * @return string
     */
    public function short(): string
    {
        return $this->default()['short'];
    }

    /**
     * Get the default weight unit's singular name.
     *
     * @return string
     */
    public function singular(): string
    {
        return $this->default()['singular'];
    }

    /**
     * Get the default weight unit's plural name.
     *
     * @return string
     */
    public function plural(): string
    {
        return $this->default()['plural'];
    }

    /**
     * Get the default weight unit's name as singular or plural.
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
     * Convert the given value to Grams.
     *
     * @param string $value
     * @return string
     */
    public function toGrams(string $value): string
    {
        if ($this->unit === 'kg') {
            return $value * 1000;
        }

        if ($this->unit === 'oz') {
            return $value / 0.03527396195;
        }

        if ($this->unit === 'lb') {
            return $value / 0.00220462262185;
        }
        
        return $value;
    }

    /**
     * Parse the weight.
     *
     * @param mixed $weight
     * @return mixed
     */
    public function parse($weight)
    {
        if (Str::startsWith($weight, '-')) {
            return null;
        }

        if ($weight === '0') {
            return null;
        }

        return $weight;
    }
}
