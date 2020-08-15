<?php

namespace Aerni\Snipcart\Support;

use Aerni\Snipcart\Facades\Dimension;
use Statamic\Entries\Entry;
use Statamic\Facades\Site;
use UnitConverter\UnitConverter;

class Converter
{
    /**
     * The default site's length unit.
     *
     * @var string
     */
    protected $defaultLengthUnit;

    /**
     * The default site's weight unit.
     *
     * @var string
     */
    protected $defaultWeightUnit;

    public function __construct()
    {
        $this->defaultLengthUnit = Dimension::from(Site::default())
            ->type('length')
            ->short();

        $this->defaultWeightUnit = Dimension::from(Site::default())
            ->type('weight')
            ->short();
    }

    /**
     * Convert a value from one unit to another.
     *
     * @param string|null $value
     * @param string|null $from
     * @param string|null $to
     * @return string|null
     */
    public function convert(?string $value, ?string $from, ?string $to)
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
     * Convert a length value to the default site's unit.
     *
     * @param mixed $value
     * @param mixed $from
     * @return string|null
     */
    public function convertToDefaultLength($value, $from)
    {
        return $this->convert($value, $from, $this->defaultLengthUnit);
    }

    /**
     * Convert a weight value to the default site's unit.
     *
     * @param mixed $value
     * @param mixed $from
     * @return string|null
     */
    public function convertToDefaultWeight($value, $from)
    {
        return $this->convert($value, $from, $this->defaultWeightUnit);
    }

    /**
     * Convert a length value to centimeters.
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
     * Convert a weight value to grams.
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
     * Convert the length/weight of an entry to the default site's unit.
     *
     * @param Entry $entry
     * @return void
     */
    public function convertEntryDimensions(Entry $entry): void
    {
        if (! $entry->hasOrigin()) {

            $data = $entry->data();

            $entryLengthUnit = $data->get('length_unit');
            $entryWeightUnit = $data->get('weight_unit');

            $length = $data->get('length');
            $width = $data->get('width');
            $height = $data->get('height');
            $weight = $data->get('weight');

            $convertedLength = $this->convertToDefaultLength($length, $entryLengthUnit);
            $convertedWidth = $this->convertToDefaultLength($width, $entryLengthUnit);
            $convertedHeight = $this->convertToDefaultLength($height, $entryLengthUnit);
            $convertedWeight = $this->convertToDefaultWeight($weight, $entryWeightUnit);

            $entry->set('length', $convertedLength);
            $entry->set('width', $convertedWidth);
            $entry->set('height', $convertedHeight);
            $entry->set('weight', $convertedWeight);

            $entry->set('length_unit', $this->defaultLengthUnit);
            $entry->set('weight_unit', $this->defaultWeightUnit);

            $entry->save();

        }

    }

    /**
     * Check if there is a value.
     *
     * @param mixed $key
     * @return bool
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
