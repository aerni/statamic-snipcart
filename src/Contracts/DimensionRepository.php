<?php

namespace Aerni\Snipcart\Contracts;

use Statamic\Sites\Site;

interface DimensionRepository
{
    public function from(Site $site): self;

    public function type(string $dimension): self;

    public function all(): array;

    public function get(string $key): string;

    public function short(): string;

    public function singular(): string;

    public function plural(): string;

    public function name(?string $value): string;

    public function parse(?string $value);
}
