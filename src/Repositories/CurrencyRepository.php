<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\CurrencyRepository as CurrencyRepositoryContract;
use Aerni\Snipcart\Exceptions\UnsupportedCurrencyException;
use Aerni\Snipcart\Models\Currency;
use Cknow\Money\Money;

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

        if (is_null($currency)) {
            throw new UnsupportedCurrencyException($this->currency);
        }

        return $currency->only(['code', 'name', 'symbol']);
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
     * Parse integer to decimal string.
     *
     * @param int|null $value
     * @return string|null
     */
    public function formatByDecimal(int $value = null)
    {
        if (is_null($value)) {
            return null;
        }

        return (string) Money::USD($value)->absolute()->formatByDecimal();
    }

    /**
     * Parse decimal string to integer.
     *
     * @param string|null $value
     * @return int|null
     */
    public function parseByDecimal(string $value = null)
    {
        if (is_null($value)) {
            return null;
        }

        return (int) Money::parseByDecimal($value, $this->currency)->absolute()->getAmount();
    }
}
