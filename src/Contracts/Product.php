<?php

namespace Aerni\Snipcart\Contracts;

use Illuminate\Support\Collection;

interface Product
{
    public function params(Collection $params = null): Collection|self;

    public function toHtmlDataString(): string;
}
