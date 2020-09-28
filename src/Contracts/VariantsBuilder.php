<?php

namespace Aerni\Snipcart\Contracts;

use Illuminate\Support\Collection;

interface VariantsBuilder
{
    public function context(Collection $context): self;

    public function params(Collection $params): self;

    public function get(): array;

    public function all(): array;
}
