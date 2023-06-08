<?php

namespace Aerni\Snipcart\Support;

use UnitConverter\UnitConverter;

class Converter
{
    /**
     * Convert a value from one unit to another.
     */
    public function convert(?string $value, ?string $from, ?string $to): ?string
    {
        if (! $this->shouldConvertValue($from, $to)) {
            return $value;
        }

        return UnitConverter::binary()
            ->convert($value)
            ->from($from)
            ->to($to);
    }

    /**
     * Convert a length value to centimeters.
     */
    public function toCentimeters(?string $value, ?string $from): ?string
    {
        return $this->convert($value, $from, 'cm');
    }

    /**
     * Convert a weight value to grams.
     */
    public function toGrams(?string $value, ?string $from): ?string
    {
        return $this->convert($value, $from, 'g');
    }

    /**
     * Check if the dimension should be converted.
     */
    protected function shouldConvertValue(?string $from, ?string $to): bool
    {
        if (empty($from) || empty($to)) {
            return false;
        }

        if ($from === $to) {
            return false;
        }

        return true;
    }
}
