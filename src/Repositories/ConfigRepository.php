<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\ConfigRepository as Contract;
use Aerni\Snipcart\Exceptions\ApiKeyNotFoundException;
use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;

class ConfigRepository implements Contract
{
    /**
     * Get the Snipcart API Key by mode.
     */
    public function apiKey(): string
    {
        $inTestMode = config('snipcart.test_mode');

        $apiKey = $inTestMode
            ? config('snipcart.test_key')
            : config('snipcart.live_key');

        if (! $apiKey) {
            throw new ApiKeyNotFoundException($inTestMode);
        }

        return $apiKey;
    }

    /**
     * Get the currency code of the current site.
     */
    public function currency(): string
    {
        return Currency::from(Site::current())->code();
    }
}
