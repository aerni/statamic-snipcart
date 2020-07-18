<?php

namespace Aerni\Snipcart\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Weight extends Model
{
    use Sushi;

    protected $rows = [
        ['abbr' => 'g', 'singular' => 'Gram', 'plural' => 'Grams'],
        ['abbr' => 'kg', 'singular' => 'Kilogram', 'plural' => 'Kilograms'],
        ['abbr' => 'oz', 'singular' => 'Ounce', 'plural' => 'Ounces'],
        ['abbr' => 'lb', 'singular' => 'Pound', 'plural' => 'Pounds'],
    ];
}
