<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\CurrencyRepository as CurrencyRepositoryContract;
use Aerni\Snipcart\Models\Currency;

class CurrencyRepository implements CurrencyRepositoryContract
{
    /**
     * Get an array of the default currency.
     *
     * @return object
     */
    public function default(): array
    {
        return Currency::firstWhere('code', config('snipcart.default_currency'))
            ->only(['code', 'name', 'symbol']);
    }

    /**
     * Get the default currency's code.
     *
     * @return string
     */
    public function code(): string
    {
        return $this->default()['code'];
    }

    /**
     * Get the default currency's name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->default()['name'];
    }

    /**
     * Get the default currency's symbol.
     *
     * @return string
     */
    public function symbol(): string
    {
        return $this->default()['symbol'];
    }
}