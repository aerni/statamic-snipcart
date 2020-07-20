<?php

namespace Aerni\Snipcart\Contracts;

interface CurrencyRepository
{
    public function default(): array;

    public function code(): string;

    public function name(): string;

    public function symbol(): string;

    public function parse($amount);
}
