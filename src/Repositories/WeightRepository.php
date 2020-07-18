<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Models\Weight;
use Exception;
use Illuminate\Support\Str;

class WeightRepository
{
    /**
     * The weight unit from the config.
     *
     * @var string
     */
    protected $unit;

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
        $unit = Weight::firstWhere('abbr', $this->unit);
        
        if (!is_null($unit)) {
            return $unit->only(['abbr', 'singular', 'plural']);
        }

        throw new Exception('This weight unit is not supported. Please make sure to set a supported unit in your config.');
    }

    /**
     * Get the default weight unit's abbreviation.
     *
     * @return string
     */
    public function abbr(): string
    {
        return $this->default()['abbr'];
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
     * Convert the given number to Grams.
     *
     * @param string $data
     * @return string
     */
    public function toGrams(string $number): string
    {
        if ($this->unit === 'kg') {
            return $number * 1000;
        }

        if ($this->unit === 'oz') {
            return round($number / 0.03527396195, 2);
        }

        if ($this->unit === 'lb') {
            return round($number / 0.00220462262185, 2);
        }
        
        return $number;
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
