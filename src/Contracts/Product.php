<?php

namespace Aerni\Snipcart\Contracts;

use Illuminate\Support\Collection;

interface Product
{
    public function params(Collection $params = null): Collection|self;

    public function variant(array $variations = null): Collection|self;

    public function toHtmlDataString(): string;

    public function rootEntryVariations(): Collection;

    public function variantWithKeys(): Collection;
}
