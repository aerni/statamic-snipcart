<?php

namespace Aerni\Snipcart\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Length extends Model
{
    use Sushi;

    public function getRows()
    {
        return [
            [
                'abbr' => 'cm', 
                'singular' => __('snipcart::units.centimeter'), 
                'plural' => __('snipcart::units.centimeters')
            ],
            [
                'abbr' => 'm', 
                'singular' => __('snipcart::units.meter'), 
                'plural' => __('snipcart::units.meters')
            ],
            [
                'abbr' => 'in', 
                'singular' => __('snipcart::units.inch'), 
                'plural' => __('snipcart::units.inches')
            ],
            [
                'abbr' => 'ft', 
                'singular' => __('snipcart::units.foot'), 
                'plural' => __('snipcart::units.feet')
            ],
        ];
    }
}
