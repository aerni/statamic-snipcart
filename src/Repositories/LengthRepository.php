<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Models\Length;
use Exception;
use Illuminate\Support\Str;

class LengthRepository
{
    /**
     * The length unit from the config.
     *
     * @var string
     */
    protected $unit;

    public function __construct()
    {
        $this->unit = config('snipcart.length');
    }

    /**
     * Get an array of the length unit.
     *
     * @return object
     */
    public function default(): array
    {
        $unit = Length::firstWhere('abbr', $this->unit);
        
        if (!is_null($unit)) {
            return $unit->only(['abbr', 'singular', 'plural']);
        }

        throw new Exception('This length unit is not supported. Please make sure to set a supported unit in your config.');
    }

    /**
     * Get the default length unit's abbreviation.
     *
     * @return string
     */
    public function abbr(): string
    {
        return $this->default()['abbr'];
    }

    /**
     * Get the default length unit's singular name.
     *
     * @return string
     */
    public function singular(): string
    {
        return $this->default()['singular'];
    }

    /**
     * Get the default length unit's plural name.
     *
     * @return string
     */
    public function plural(): string
    {
        return $this->default()['plural'];
    }

    /**
     * Convert the given number to Centimeters.
     *
     * @param string $data
     * @return string
     */
    public function toCentimeters(string $number): string
    {
        if ($this->unit === 'm') {
            return $number * 100;
        }

        if ($this->unit === 'in') {
            return round($number / 0.3937007874, 2);
        }

        if ($this->unit === 'ft') {
            return round($number / 0.032808399, 2);
        }
        
        return $number;
    }

    /**
     * Parse the length.
     *
     * @param mixed $length
     * @return mixed
     */
    public function parse($length)
    {
        if (Str::startsWith($length, '-')) {
            return null;
        }

        if ($length === '0') {
            return null;
        }

        return $length;
    }
}
