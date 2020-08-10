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
     * Get an array of the currency's information.
     *
     * @return object
     */
    public function all(): array
    {
        $currency = Currency::firstWhere('code', $this->currency);

        if (! is_null($currency)) {
            return $currency->only(['code', 'name', 'symbol']);
        }

        throw new Exception('This currency is not supported. Please make sure to set a supported currency in your config.');
    }

    /**
     * Get the currency's code.
     *
     * @return string
     */
    public function code(): string
    {
        return $this->all()['code'];
    }

    /**
     * Get the currency's name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->all()['name'];
    }

    /**
     * Get the currency's symbol.
     *
     * @return string
     */
    public function symbol(): string
    {
        return $this->all()['symbol'];
    }

    /**
     * Parse the value to two decimal places.
     *
     * @param mixed $value
     * @return mixed
     */
    public function parse($value)
    {
        if (Str::startsWith($value, '-')) {
            return '0.00';
        }

        if (is_null($value)) {
            return null;
        }

        return number_format(floatval($value), 2, '.', '');
    }
}
