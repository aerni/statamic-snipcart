<?php

namespace Aerni\Snipcart\Fieldtypes;

use Statamic\Fields\Fieldtype;

class StockFieldtype extends Fieldtype
{
    /**
     * Process the data before it gets saved.
     */
    public function process(mixed $data): int
    {
        return $data;
    }
}
