<?php

namespace Aerni\Snipcart\Contracts;

use Illuminate\Support\Collection;

interface Product
{
    public function params(Collection $params = null);

    public function variant(array $options = null);

    public function toHtmlDataString(): string;

    public function rootEntryVariations(): Collection;

    public function variantWithKeys(): Collection;
}
