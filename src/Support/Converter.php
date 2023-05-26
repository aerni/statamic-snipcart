<?php

namespace Aerni\Snipcart\Support;

use UnitConverter\UnitConverter;

class Converter
{
    // TODO: Can we move away from using 'mixed' as param type for all methods

    /**
     * Convert a value from one unit to another.
     */
    public function convert(?string $value, ?string $from, ?string $to): ?string
    {
        if ($this->hasValue($value) && $this->canConvert($from, $to)) {
            return UnitConverter::binary()
                ->convert($value)
                ->from($from)
                ->to($to);
        }

        if ($this->hasValue($value)) {
            return $value;
        }

        return null;
    }

    /**
     * Convert a length value to centimeters.
     */
    public function toCentimeters(mixed $value, mixed $from): ?string
    {
        return $this->convert($value, $from, 'cm');
    }

    /**
     * Convert a weight value to grams.
     */
    public function toGrams(mixed $value, mixed $from): ?string
    {
        return $this->convert($value, $from, 'g');
    }

    /**
     * Check if there is a value.
     */
    protected function hasValue(mixed $value): bool
    {
        return empty($value) ? false : true;
    }

    /**
     * Check if the dimension can be converted.
     */
    protected function canConvert(mixed $from, mixed $to): bool
    {
        if (empty($from)) {
            return false;
        }

        if (empty($to)) {
            return false;
        }

        if ($from === $to) {
            return false;
        }

        return true;
    }
}
