<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\CurrencyRepository as Contract;
use Aerni\Snipcart\Exceptions\SitesNotInSyncException;
use Aerni\Snipcart\Exceptions\UnsupportedCurrencyException;
use Aerni\Snipcart\Models\Currency as CurrencyModel;
use Illuminate\Support\Facades\Config;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use Money\Parser\IntlMoneyParser;
use NumberFormatter;
use Statamic\Facades\Site as SiteFacade;
use Statamic\Sites\Site;

class CurrencyRepository implements Contract
{
    /**
     * The site to get the currency from.
     */
    protected Site $site;

    /**
     * Set the site property.
     */
    public function from(Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get an array of the currency's data.
     */
    public function data(): array
    {
        $sites = collect(Config::get('snipcart.sites'));

        if (! $sites->has($this->site->handle())) {
            throw new SitesNotInSyncException($this->site->handle());
        }

        $currencySetting = $sites->get($this->site->handle())['currency'];

        $currency = CurrencyModel::firstWhere('code', $currencySetting);

        if (is_null($currency)) {
            throw new UnsupportedCurrencyException($this->site->handle(), $currencySetting);
        }

        return $currency;
    }

    /**
     * Get an array of the currency's data from all the sites.
     */
    public function all(): array
    {
        $currencySettings = SiteFacade::all()->map(function ($item, $key) {
            $sites = collect(Config::get('snipcart.sites'));

            if (! $sites->has($key)) {
                throw new SitesNotInSyncException($key);
            }

            return $sites->get($key)['currency'];
        });

        $currencies = $currencySettings->map(function ($item) {
            return CurrencyModel::firstWhere('code', $item);
        })->toArray();

        return $currencies;
    }

    /**
     * Get a currency value by key.
     */
    public function get(string $key): string
    {
        return $this->data()[$key];
    }

    /**
     * Get the currency's code.
     */
    public function code(): string
    {
        return $this->get('code');
    }

    /**
     * Get the currency's symbol.
     */
    public function symbol(): string
    {
        return $this->get('symbol');
    }

    /**
     * Get the currency's name.
     */
    public function name(): string
    {
        return $this->get('name');
    }

    /**
     * Format an integer to an international currency string.
     * e.g. 1000 -> $10.00
     * e.g. null -> $0.00
     */
    public function formatCurrency(?int $value): string
    {
        $money = new Money($value, new Currency($this->code()));
        $numberFormatter = new NumberFormatter($this->site->locale(), NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * Parse an international currency string to an integer.
     * e.g. $10.00 -> 1000
     */
    public function parseCurrency(string $value): int
    {
        $numberFormatter = new NumberFormatter($this->site->locale(), NumberFormatter::CURRENCY);
        $moneyParser = new IntlMoneyParser($numberFormatter, new ISOCurrencies());

        return $moneyParser->parse($value)->getAmount();
    }

    /**
     * Format an integer to a decimal string.
     * e.g. 1000 -> 10.00
     * e.g. null -> null
     */
    public function formatDecimal(?int $value): ?string
    {
        if (is_null($value)) {
            return $value;
        }

        $money = new Money($value, new Currency($this->code()));
        $moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());

        return $moneyFormatter->format($money);
    }

    /**
     * Parse a decimal string to an integer.
     * e.g. 10.00 -> 1000
     * e.g. null -> 0
     */
    public function parseDecimal(?string $value): int
    {
        if (is_null($value)) {
            return (int) $value;
        }

        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());

        return $moneyParser->parse($value, new Currency($this->code()))->getAmount();
    }
}
