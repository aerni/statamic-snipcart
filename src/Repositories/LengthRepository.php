<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\LengthRepository as LengthRepositoryContract;
use Aerni\Snipcart\Models\Length;
use Exception;
use Illuminate\Support\Str;

class LengthRepository implements LengthRepositoryContract
{
    /**
     * The length unit from the config.
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
        $this->unit = config('snipcart.length');
    }

    /**
     * Get an array of the length unit.
     *
     * @return object
     */
    public function default(): array
    {
        $unit = Length::firstWhere('short', $this->unit);
        
        if (! is_null($unit)) {
            return $unit->only(['short', 'singular', 'plural']);
        }

        throw new Exception('This length unit is not supported. Please make sure to set a supported unit in your config.');
    }

    /**
     * Get the default length unit's abbreviation.
     *
     * @return string
     */
    public function short(): string
    {
        return $this->default()['short'];
    }

    /**
     * Get the default length unit's name as singular or plural.
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
     * Convert the given value to Centimeters.
     *
     * @param string $value
     * @return string
     */
    public function toCentimeters(string $value): string
    {
        if ($this->unit === 'm') {
            return $value * 100;
        }

        if ($this->unit === 'in') {
            return round($value / 0.3937007874, 2);
        }

        if ($this->unit === 'ft') {
            return round($value / 0.032808399, 2);
        }
        
        return $value;
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
