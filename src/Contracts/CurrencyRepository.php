<?php

namespace Aerni\Snipcart\Contracts;

interface CurrencyRepository
{
    public function all(): array;

    public function code(): string;

    public function name(): string;

    public function symbol(): string;

    public function formatBydecimal(int $value = null);

    public function parseBydecimal(string $value = null);
}
