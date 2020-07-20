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
                'abbr' => 'g',
                'singular' => __('snipcart::units.gram'),
                'plural' => __('snipcart::units.grams'),
            ],
            [
                'abbr' => 'kg',
                'singular' => __('snipcart::units.kilogram'),
                'plural' => __('snipcart::units.kilograms'),
            ],
            [
                'abbr' => 'oz',
                'singular' => __('snipcart::units.ounce'),
                'plural' => __('snipcart::units.ounces'),
            ],
            [
                'abbr' => 'lb',
                'singular' => __('snipcart::units.pound'),
                'plural' => __('snipcart::units.pounds'),
            ],
        ];
    }
}
