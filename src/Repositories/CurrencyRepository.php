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
    protected $unit;

    public function __construct()
    {
        $this->unit = config('snipcart.currency');
    }

    /**
     * Get an array of the default currency.
     *
     * @return object
     */
    public function default(): array
    {
        $unit = Currency::firstWhere('code', $this->unit);

        if (!is_null($unit)) {
            return $unit->only(['code', 'name', 'symbol']);
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
     * Parse the price to two decimal places.
     *
     * @param mixed $price
     * @return mixed
     */
    public function parse($price)
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
