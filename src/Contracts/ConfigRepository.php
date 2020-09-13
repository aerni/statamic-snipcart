<?php

namespace Aerni\Snipcart\Contracts;

interface ConfigRepository
{
    public function apiKey(): string;

    public function currency(): string;
}
