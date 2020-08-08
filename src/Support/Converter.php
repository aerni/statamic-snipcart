<?php

namespace Aerni\Snipcart\Support;

use Statamic\Entries\Entry;
use UnitConverter\UnitConverter;

class Converter
{
    /**
     * Convert a value from on unit to another.
     *
     * @param string $value
     * @param string $from
     * @param string $to
     * @return string|null
     */
    public function convert(string $value = null, string $from = null, string $to = null)
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
     * Convert a length to the unit set in the config.
     *
     * @param mixed $value
     * @param mixed $from
     * @return string|null
     */
    public function convertLength($value, $from)
    {
        return $this->convert($value, $from, config('snipcart.length'));
    }

    /**
     * Convert a weight to the unit set in the config.
     *
     * @param mixed $value
     * @param mixed $from
     * @return string|null
     */
    public function convertWeight($value, $from)
    {
        return $this->convert($value, $from, config('snipcart.weight'));
    }

    /**
     * Convert a value to centimeters.
     *
     * @param mixed $value
     * @param mixed $from
     * @return string|null
     */
    public function toCentimeters($value, $from)
    {
        return $this->convert($value, $from, 'cm');
    }

    /**
     * Convert a value to grams.
     *
     * @param mixed $value
     * @param mixed $from
     * @return string|null
     */
    public function toGrams($value, $from)
    {
        return $this->convert($value, $from, 'g');
    }

    /**
     * Convert length and weight units of an entry.
     *
     * @param Entry $entry
     * @return void
     */
    public function convertEntryDimensions(Entry $entry): void
    {
        $data = $entry->data();

        $entryLengthUnit = $data->get('length_unit');
        $entryWeightUnit = $data->get('weight_unit');

        $length = $data->get('length');
        $width = $data->get('width');
        $height = $data->get('height');
        $weight = $data->get('weight');
        
        $convertedLength = $this->convertLength($length, $entryLengthUnit);
        $convertedWidth = $this->convertLength($width, $entryLengthUnit);
        $convertedHeight = $this->convertLength($height, $entryLengthUnit);
        $convertedWeight = $this->convertWeight($weight, $entryWeightUnit);

        $entry->set('length', $convertedLength);
        $entry->set('width', $convertedWidth);
        $entry->set('height', $convertedHeight);
        $entry->set('weight', $convertedWeight);

        $entry->set('length_unit', config('snipcart.length'));
        $entry->set('weight_unit', config('snipcart.weight'));

        $entry->save();
    }

    /**
     * Check if there is a value.
     *
     * @param mixed $key
     * @return boolean
     */
    protected function hasValue($value): bool
    {
        if (empty($value)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the dimension can be converted.
     *
     * @param mixed $from
     * @param mixed $to
     * @return bool
     */
    protected function canConvert($from, $to): bool
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