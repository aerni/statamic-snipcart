<?php

namespace Aerni\Snipcart\Contracts;

interface ConfigRepository
{
    /**
     * Get the Snipcart API Key by mode.
     *
     * @return string
     */
    public function apiKey(): string;

    /**
     * Get the currency code of the current site.
     *
     * @return string
     */
    public function currency(): string;
}
