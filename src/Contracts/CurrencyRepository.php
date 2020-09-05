<?php

namespace Aerni\Snipcart\Contracts;

use Statamic\Sites\Site;

interface CurrencyRepository
{
    public function from(Site $site): self;

    public function data(): array;

    public function all(): array;

    public function get(string $key): string;

    public function code(): string;

    public function symbol(): string;

    public function name(): string;

    public function formatCurrency(?int $value): string;

    public function parseCurrency(string $value): int;

    public function formatDecimal(?int $value): string;

    public function parseDecimal(?string $value): int;

    public function formatDecimalIntl(?int $value): string;

    public function parseDecimalIntl(?string $value): int;
}
