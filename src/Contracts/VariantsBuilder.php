<?php

namespace Aerni\Snipcart\Contracts;

use Illuminate\Support\Collection;

interface VariantsBuilder
{
    public function context(Collection $context): self;

    public function all(): array;
}
