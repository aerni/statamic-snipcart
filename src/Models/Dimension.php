<?php

namespace Aerni\Snipcart\Models;

use Aerni\Snipcart\Models\Model;

class Dimension extends Model
{
    protected static function getRows(): array
    {
        return [
            [
                'dimension' => 'length',
                'short' => 'cm',
                'singular' => __('snipcart::units.centimeter'),
                'plural' => __('snipcart::units.centimeters'),
            ],
            [
                'dimension' => 'length',
                'short' => 'm',
                'singular' => __('snipcart::units.meter'),
                'plural' => __('snipcart::units.meters'),
            ],
            [
                'dimension' => 'length',
                'short' => 'in',
                'singular' => __('snipcart::units.inch'),
                'plural' => __('snipcart::units.inches'),
            ],
            [
                'dimension' => 'length',
                'short' => 'ft',
                'singular' => __('snipcart::units.foot'),
                'plural' => __('snipcart::units.feet'),
            ],
            [
                'dimension' => 'weight',
                'short' => 'g',
                'singular' => __('snipcart::units.gram'),
                'plural' => __('snipcart::units.grams'),
            ],
            [
                'dimension' => 'weight',
                'short' => 'kg',
                'singular' => __('snipcart::units.kilogram'),
                'plural' => __('snipcart::units.kilograms'),
            ],
            [
                'dimension' => 'weight',
                'short' => 'oz',
                'singular' => __('snipcart::units.ounce'),
                'plural' => __('snipcart::units.ounces'),
            ],
            [
                'dimension' => 'weight',
                'short' => 'lb',
                'singular' => __('snipcart::units.pound'),
                'plural' => __('snipcart::units.pounds'),
            ],
        ];
    }
}
