<?php

namespace Aerni\Snipcart\Modifiers;

use Statamic\Modifiers\Modifier;
use Statamic\Support\Str;

class AddOperator extends Modifier
{
    /**
     * Add + or - operator to a value.
     */
    public function index(string $value): string
    {
        if (Str::startsWith($value, '-')) {
            return $value;
        }

        return Str::ensureRight('+', $value);
    }
}
