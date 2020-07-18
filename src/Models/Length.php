<?php

namespace Aerni\Snipcart\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Length extends Model
{
    use Sushi;

    protected $rows = [
        ['abbr' => 'cm', 'singular' => 'Centimeter', 'plural' => 'Centimeters'],
        ['abbr' => 'm', 'singular' => 'Meter', 'plural' => 'Meters'],
        ['abbr' => 'in', 'singular' => 'Inch', 'plural' => 'Inches'],
        ['abbr' => 'ft', 'singular' => 'Foot', 'plural' => 'Feet'],
    ];
}
