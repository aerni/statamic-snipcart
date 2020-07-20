<?php

namespace Aerni\Snipcart\Contracts;

interface WeightRepository
{
    public function default(): array;

    public function short(): string;

    public function singular(): string;

    public function plural(): string;

    public function toGrams(string $value): string;

    public function parse($weight);
}
