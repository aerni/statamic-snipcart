<?php

namespace Aerni\Snipcart\Contracts;

interface LengthRepository
{
    public function default(): array;

    public function short(): string;

    public function name($value): string;

    public function singular(): string;

    public function plural(): string;

    public function parse($length);
}
