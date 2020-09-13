<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\ConfigRepository as ConfigRepositoryContract;
use Aerni\Snipcart\Exceptions\ApiKeyNotFoundException;
use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;

class ConfigRepository implements ConfigRepositoryContract
{
    /**
     * Returns the Snipcart API Key.
     *
     * @return string
     */
    public function apiKey(): string
    {
        $mode = config('snipcart.test_mode');

        $apiKey = $mode
            ? config('snipcart.test_key')
            : config('snipcart.live_key');

        if (! $apiKey) {
            throw new ApiKeyNotFoundException($mode);
        }

        return $apiKey;
    }

    /**
     * Returns the currency of the current site.
     *
     * @return string
     */
    public function currency(): string
    {
        return Currency::from(Site::current())->code();
    }
}
