<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\CurrencyRepository as CurrencyRepositoryContract;
use Aerni\Snipcart\Models\Currency;
use Exception;
use Illuminate\Support\Str;

class CurrencyRepository implements CurrencyRepositoryContract
{
    /**
     * The currency from the config.
     *
     * @var string
     */
    protected $currency;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->currency = config('snipcart.currency');
    }

    /**
     * Get an array of the default currency.
     *
     * @return object
     */
    public function default(): array
    {
        $currency = Currency::firstWhere('code', $this->currency);

        if (! is_null($currency)) {
            return $currency->only(['code', 'name', 'symbol']);
        }

        throw new Exception('This currency is not supported. Please make sure to set a supported currency in your config.');
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

    /**
     * Parse the amount to two decimal places.
     *
     * @param mixed $amount
     * @return mixed
     */
    public function parse($amount)
    {
        if (Str::startsWith($amount, '-')) {
            return '0.00';
        }

        if (is_null($amount)) {
            return null;
        }

        return number_format(floatval($amount), 2, '.', '');
    }
}
