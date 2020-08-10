<?php

namespace Aerni\Snipcart\Contracts;

interface DimensionRepository
{
    public function type(string $type): self;

    public function all(): array;

    public function short(): string;

    public function singular(): string;

    public function plural(): string;

    public function name($value): string;

    public function parse($value);
}
