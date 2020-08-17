<?php

namespace Aerni\Snipcart\Repositories;

use Aerni\Snipcart\Contracts\CurrencyRepository as CurrencyRepositoryContract;
use Aerni\Snipcart\Exceptions\SitesNotInSyncException;
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
use Statamic\Facades\Site as SiteFacade;
use Statamic\Sites\Site;

class CurrencyRepository implements CurrencyRepositoryContract
{
    /**
     * The site to get the currency from.
     *
     * @var Site
     */
    protected $site;

    /**
     * Set the site property.
     *
     * @param Site $site
     */
    public function from(Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get an array of the currency's data.
     *
     * @return array
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

        return $currency->toArray();
    }

    /**
     * Get an array of the currency's data from all the sites.
     *
     * @return array
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
            return CurrencyModel::firstWhere('code', $item)->toArray();
        })->toArray();

        return $currencies;
    }

    /**
     * Get a currency value by key.
     *
     * @return string
     */
    public function get(string $key): string
    {
        return $this->data()[$key];
    }

    /**
     * Get the currency's code.
     *
     * @return string
     */
    public function code(): string
    {
        return $this->get('code');
    }

    /**
     * Get the currency's symbol.
     *
     * @return string
     */
    public function symbol(): string
    {
        return $this->get('symbol');
    }

    /**
     * Get the currency's name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->get('name');
    }

    /**
     * Format an integer to a currency string.
     *
     * @param int|null $value
     * @return string|null
     */
    public function formatCurrency(?int $value)
    {
        if (is_null($value)) {
            return $value;
        }

        $money = new Money($value, new Currency($this->code()));
        $numberFormatter = new NumberFormatter($this->site->locale(), NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return (string) $moneyFormatter->format($money);
    }

    /**
     * Format an integer to a decimal string.
     *
     * @param int|null $value
     * @return string|null
     */
    public function formatDecimal(?int $value)
    {
        if (is_null($value)) {
            return $value;
        }

        $money = new Money($value, new Currency($this->code()));
        $moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());

        return (string) $moneyFormatter->format($money);
    }

    /**
     * Format an integer to a decimal string.
     *
     * @param int|null $value
     * @return string|null
     */
    public function formatDecimalIntl(?int $value)
    {
        if (is_null($value)) {
            return $value;
        }

        $money = new Money($value, new Currency($this->code()));
        $numberFormatter = new NumberFormatter($this->site->locale(), NumberFormatter::DECIMAL);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return (string) $moneyFormatter->format($money);
    }

    /**
     * Parse a decimal string to an integer.
     *
     * @param string|null $value
     * @return int|null
     */
    public function parseDecimal(?string $value)
    {
        if (is_null($value)) {
            return $value;
        }

        $numberFormatter = new NumberFormatter($this->site->locale(), NumberFormatter::DECIMAL);
        $moneyParser = new IntlLocalizedDecimalParser($numberFormatter, new ISOCurrencies());

        return (int) $moneyParser->parse($value, new Currency($this->code()))->getAmount();
    }
}
