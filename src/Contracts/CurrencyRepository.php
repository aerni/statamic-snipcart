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

    public function formatCurrency(?int $value);

    public function formatDecimal(?int $value);

    public function formatDecimalIntl(?int $value);

    public function parseDecimal(?string $value);
}
