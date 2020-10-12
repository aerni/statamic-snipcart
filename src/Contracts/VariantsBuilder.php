<?php

namespace Aerni\Snipcart\Contracts;

use Statamic\Entries\Entry;

interface VariantsBuilder
{
    public function process(Entry $entry): ?array;
}
