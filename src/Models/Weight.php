<?php

namespace Aerni\Snipcart\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Weight extends Model
{
    use Sushi;

    public function getRows()
    {
        return [
            [
                'short' => 'g',
                'singular' => __('snipcart::units.gram'),
                'plural' => __('snipcart::units.grams'),
            ],
            [
                'short' => 'kg',
                'singular' => __('snipcart::units.kilogram'),
                'plural' => __('snipcart::units.kilograms'),
            ],
            [
                'short' => 'oz',
                'singular' => __('snipcart::units.ounce'),
                'plural' => __('snipcart::units.ounces'),
            ],
            [
                'short' => 'lb',
                'singular' => __('snipcart::units.pound'),
                'plural' => __('snipcart::units.pounds'),
            ],
        ];
    }
}
