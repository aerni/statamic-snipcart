<?php

namespace Aerni\Snipcart\Modifiers;

use Statamic\Modifiers\Modifier;

class StripUnit extends Modifier
{
    /**
     * Remove units and operators from a value.
     *
     * @param string $value
     * @return mixed
     */
    public function index(string $value): string
    {
        return preg_replace('/[^0-9,.+-]/', '', $value);
    }
}
