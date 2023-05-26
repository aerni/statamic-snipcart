<?php

namespace Aerni\Snipcart\Contracts;

interface ConfigRepository
{
    /**
     * Get the Snipcart API Key by mode.
     */
    public function apiKey(): string;

    /**
     * Get the currency code of the current site.
     */
    public function currency(): string;
}
