<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\CurrencyRepository as CurrencyRepositoryContract;
use Aerni\Snipcart\Models\Currency;
use Illuminate\Support\Str;

class CurrencyRepository implements CurrencyRepositoryContract
{
    /**
     * Get an array of the default currency.
     *
     * @return object
     */
    public function default(): array
    {
        $defaultCurrencyCode = config('snipcart.default_currency');

        $currency = Currency::where('code', $defaultCurrencyCode)->first();

        if (!is_null($currency)) {
            return $currency->only(['code', 'name', 'symbol']);
        }

        return ['code' => $defaultCurrencyCode];
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
        return $this->default()['name'] ?? '';
    }

    /**
     * Get the default currency's symbol.
     *
     * @return string
     */
    public function symbol(): string
    {
        return $this->default()['symbol'] ?? '';
    }

    /**
     * Parse the price to two decimal places.
     *
     * @param mixed $price
     * @return mixed
     */
    public static function parse($price)
    {
        if (Str::startsWith($price, '-')) {
            return '0.00';
        }

        if (is_null($price)) {
            return null;
        }

        return number_format(floatval($price), 2, '.', '');
    }
}
