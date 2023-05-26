<?php

namespace Aerni\Snipcart\Repositories;

use Locale;
use Statamic\Sites\Site;
use Illuminate\Support\Str;
use Illuminate\Support\Pluralizer;
use Aerni\Snipcart\Models\Dimension;
use Statamic\Facades\Site as SiteApi;
use Illuminate\Support\Facades\Config;
use Aerni\Snipcart\Exceptions\SitesNotInSyncException;
use Aerni\Snipcart\Contracts\DimensionRepository as Contract;
use Aerni\Snipcart\Exceptions\UnsupportedDimensionTypeException;
use Aerni\Snipcart\Exceptions\UnsupportedDimensionUnitException;
use Doctrine\Inflector\Language;
use ReflectionClass;

class DimensionRepository implements Contract
{
    /**
     * The site to get the dimension from.
     */
    protected Site $site;

    /**
     * The dimension (length/weight)
     */
    protected string $dimension;

    /**
     * Set the site property.
     */
    public function from(Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Set the dimension property
     */
    public function type(string $dimension): self
    {
        if ($dimension !== 'length' && $dimension !== 'weight') {
            throw new UnsupportedDimensionTypeException($dimension);
        }

        $this->dimension = $dimension;

        return $this;
    }

    /**
     * Get an array of the unit's data.
     */
    public function all(): array
    {
        $sites = collect(Config::get('snipcart.sites'));

        if (! $sites->has($this->site->handle())) {
            throw new SitesNotInSyncException($this->site->handle());
        }

        $unitSetting = $sites->get($this->site->handle())[$this->dimension];

        $unit = Dimension::where('dimension', $this->dimension)
            ->where('short', $unitSetting)
            ->first();

        if (is_null($unit)) {
            throw new UnsupportedDimensionUnitException($this->site->handle(), $this->dimension, $unitSetting);
        }

        return $unit;
    }

    /**
     * Get a unit value by key.
     */
    public function get(string $key): string
    {
        return $this->all()[$key];
    }

    /**
     * Get the unit's abbreviation.
     */
    public function short(): string
    {
        return $this->get('short');
    }

    /**
     * Get the unit's singular name.
     */
    public function singular(): string
    {
        return $this->get('singular');
    }

    /**
     * Get the unit's plural name.
     */
    public function plural(): string
    {
        if (! $this->canPluralizeLanguage()) {
            return $this->get('plural');
        }

        Pluralizer::useLanguage(Str::of($this->displayLanguage())->slug('-')->lower());

        return Str::plural($this->get('singular'));
    }

    /**
     * Get the unit's singular/plural name.
     */
    public function name(?string $value): string
    {
        return $value > 1
            ? $this->plural()
            : $this->singular();
    }

    /**
     * Parse the value.
     */
    public function parse(?string $value): ?string
    {
        return $value;
    }

    /**
     * Determine if the language is supported by Laravel's pluralizer.
     */
    protected function canPluralizeLanguage(): bool
    {
        $language = Str::of($this->displayLanguage())->slug('_')->upper();

        return (new ReflectionClass(Language::class))->hasConstant($language);
    }

    /**
     * Get the display language of the current site.
     */
    protected function displayLanguage(): string
    {
        return Locale::getDisplayLanguage(SiteApi::current()->shortLocale());
    }
}
