<?php

namespace Aerni\Snipcart\Contracts;

interface WeightRepository
{
    public function default(): array;

    public function short(): string;

    public function name($value): string;

    public function singular(): string;

    public function plural(): string;

    public function toGrams(string $value, string $unit): string;

    public function parse($weight);
}
