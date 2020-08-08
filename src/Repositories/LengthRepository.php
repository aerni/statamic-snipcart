<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\LengthRepository as LengthRepositoryContract;
use Aerni\Snipcart\Models\Length;
use Exception;
use Illuminate\Support\Str;
use UnitConverter\UnitConverter;

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
     * Convert a value of a unit to Centimeters.
     *
     * @param string $value
     * @param string $unit
     * @return string
     */
    public function toCentimeters(string $value, string $unit): string
    {
        return UnitConverter::default()
            ->convert($value)
            ->from($unit)
            ->to('cm');
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
