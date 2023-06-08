<?php

namespace Aerni\Snipcart\Data;

use Illuminate\Support\Collection;

class SnipcartProduct
{
    public function __construct(protected Collection $data)
    {
    }

    public function __call($name, $arguments)
    {
        return $this->data->get($name);
    }
}
