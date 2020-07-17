<?php

namespace Aerni\Snipcart\Contracts;

interface CurrencyRepository
{
    public function default(): array;
}
