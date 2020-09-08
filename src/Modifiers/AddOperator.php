<?php

namespace Aerni\Snipcart\Modifiers;

use Statamic\Support\Str;
use Statamic\Modifiers\Modifier;

class AddOperator extends Modifier
{
    /**
     * Modify a value.
     *
     * @param mixed  $value    The value to be modified
     * @param array  $params   Any parameters used in the modifier
     * @param array  $context  Contextual values
     * @return mixed
     */
    public function index($value, $params, $context)
    {
        if (Str::startsWith($value, '-')) {
            return $value;
        }

        return Str::ensureRight('+', $value);
    }
}
