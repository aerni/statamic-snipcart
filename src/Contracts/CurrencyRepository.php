<?php

namespace Aerni\Snipcart\Contracts;

use Statamic\Sites\Site;

interface CurrencyRepository
{
    public function get(Site $site): array;

    public function code(Site $site): string;

    public function symbol(Site $site): string;

    public function name(Site $site): string;

    public function formatCurrency(?int $value, Site $site);

    public function formatDecimal(?int $value, Site $site);

    public function formatDecimalIntl(?int $value, Site $site);

    public function parseDecimal(?string $value, Site $site);
}
