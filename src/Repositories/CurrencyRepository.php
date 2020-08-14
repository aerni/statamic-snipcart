<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\CurrencyRepository as CurrencyRepositoryContract;
use Aerni\Snipcart\Exceptions\UnsupportedCurrencyException;
use Aerni\Snipcart\Models\Currency as CurrencyModel;
use Illuminate\Support\Facades\Config;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\IntlLocalizedDecimalParser;
use NumberFormatter;
use Statamic\Sites\Site;

class CurrencyRepository implements CurrencyRepositoryContract
{
    /**
     * Get the currency's code, symbol and name.
     *
     * @param Site $site
     * @return array
     */
    public function get(Site $site): array
    {
        $siteSettings = collect(Config::get('snipcart.sites'))
            ->get($site->handle());

        $currency = CurrencyModel::firstWhere('code', $siteSettings['currency']);

        if (is_null($currency)) {
            throw new UnsupportedCurrencyException($site->handle(), $siteSettings['currency']);
        }

        return $currency->only(['code', 'symbol', 'name']);
    }

    /**
     * Get the currency's code.
     *
     * @param Site $site
     * @return string
     */
    public function code(Site $site): string
    {
        return $this->get($site)['code'];
    }

    /**
     * Get the currency's symbol.
     *
     * @param Site $site
     * @return string
     */
    public function symbol(Site $site): string
    {
        return $this->get($site)['symbol'];
    }

    /**
     * Get the currency's name.
     *
     * @param Site $site
     * @return string
     */
    public function name(Site $site): string
    {
        return $this->get($site)['name'];
    }

    /**
     * Format an integer to a currency string.
     *
     * @param integer|null $value
     * @param Site $site
     * @return string|null
     */
    public function formatCurrency(?int $value, Site $site)
    {
        if (is_null($value)) {
            return null;
        }

        $money = new Money($value, new Currency($this->code($site)));
        $numberFormatter = new NumberFormatter($site->locale(), NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return (string) $moneyFormatter->format($money);
    }

    /**
     * Format an integer to a decimal string.
     *
     * @param integer|null $value
     * @param Site $site
     * @return string|null
     */
    public function formatDecimal(?int $value, Site $site)
    {
        if (is_null($value)) {
            return null;
        }

        $money = new Money($value, new Currency($this->code($site)));
        $moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());

        return (string) $moneyFormatter->format($money);
    }

    /**
     * Format an integer to a decimal string.
     *
     * @param integer|null $value
     * @param Site $site
     * @return string|null
     */
    public function formatDecimalIntl(?int $value, Site $site)
    {
        if (is_null($value)) {
            return null;
        }

        $money = new Money($value, new Currency($this->code($site)));
        $numberFormatter = new NumberFormatter($site->locale(), NumberFormatter::DECIMAL);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return (string) $moneyFormatter->format($money);
    }

    /**
     * Parse a decimal string to an integer.
     *
     * @param string|null $value
     * @param Site $site
     * @return integer|null
     */
    public function parseDecimal(?string $value, Site $site)
    {
        if (is_null($value)) {
            return null;
        }

        $numberFormatter = new NumberFormatter($site->locale(), NumberFormatter::DECIMAL);
        $moneyParser = new IntlLocalizedDecimalParser($numberFormatter, new ISOCurrencies());

        return (int) $moneyParser->parse($value, new Currency($this->code($site)))->getAmount();
    }
}
