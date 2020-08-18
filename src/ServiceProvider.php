<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Exceptions\ApiKeyNotFoundException;
use Aerni\Snipcart\Facades\Currency;
use Aerni\Snipcart\Tags\SnipcartTags;
use Illuminate\Support\Facades\Config;
use Statamic\Facades\Site;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        Commands\MigrateSnipcart::class,
        Commands\SetupSnipcart::class,
    ];

    protected $fieldtypes = [
        Fieldtypes\DimensionFieldtype::class,
        Fieldtypes\StockFieldtype::class,
        Fieldtypes\MoneyFieldtype::class,
    ];

    protected $listen = [
        'Statamic\Events\EntryBlueprintFound' => [
            'Aerni\Snipcart\Listeners\ConvertDimensions',
            'Aerni\Snipcart\Listeners\MakeSkuReadOnly',
        ],
    ];

    protected $modifiers = [
        Modifiers\StripUnit::class,
    ];

    protected $routes = [
        'web' => __DIR__ . '/../routes/web.php',
    ];

    protected $scripts = [
        __DIR__.'/../resources/dist/js/cp.js',
    ];

    protected $tags = [
        Tags\CurrencyTags::class,
        Tags\LengthTags::class,
        Tags\SnipcartTags::class,
        Tags\WeightTags::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Statamic::booted(function () {
            $this->bootVendorAssets();
            $this->setSnipcartApiConfig();
            $this->setSnipcartWebhooksConfig();
        });

        Statamic::afterInstalled(function ($command) {
            $command->call('snipcart:setup');
        });
    }

    public function register(): void
    {
        parent::register();

        Statamic::booted(function () {
            $this->registerRepositories();
            $this->registerTags();
        });
    }

    /**
     * Publish the vendor assets.
     *
     * @return void
     */
    protected function bootVendorAssets(): void
    {
        $this->publishes([
            __DIR__.'/../config/snipcart.php' => config_path('snipcart.php'),
        ], 'snipcart-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/snipcart'),
        ], 'snipcart-translations');

        $this->mergeConfigFrom(__DIR__.'/../config/snipcart.php', 'snipcart');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'snipcart');
    }

    /**
     * Set the config of the Snipcart API package.
     *
     * @return void
     */
    protected function setSnipcartApiConfig(): void
    {
        $snipcartApiConfig = Config::get('snipcart-api');
        $snipcartConfig = Config::get('snipcart');

        $mergedConfigs = array_intersect_key($snipcartConfig, $snipcartApiConfig);

        foreach ($mergedConfigs as $key => $value) {
            Config::set("snipcart-api.{$key}", $value);
        }
    }

    /**
     * Set the config of the Snipcart Webhooks package.
     *
     * @return void
     */
    protected function setSnipcartWebhooksConfig(): void
    {
        $snipcartWebhooksConfig = Config::get('snipcart-webhooks');
        $snipcartConfig = Config::get('snipcart');

        $mergedConfigs = array_intersect_key($snipcartConfig, $snipcartWebhooksConfig);

        foreach ($mergedConfigs as $key => $value) {
            Config::set("snipcart-webhooks.{$key}", $value);
        }
    }

    /**
     * Bind the repositories.
     *
     * @return void
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(\Statamic\Contracts\Entries\EntryRepository::class, Repositories\EntryRepository::class);
        $this->app->bind('Converter', Support\Converter::class);
        $this->app->bind('Currency', Repositories\CurrencyRepository::class);
        $this->app->bind('Dimension', Repositories\DimensionRepository::class);
        $this->app->bind('Product', Repositories\ProductRepository::class);
    }

    /**
     * Bind the tags.
     *
     * @return void
     */
    protected function registerTags(): void
    {
        $this->app->bind(SnipcartTags::class, function () {
            return new SnipcartTags([
                'key' => $this->apiKey(),
                'currency' => $this->currency(),
                'version' => config('snipcart.version'),
                'behaviour' => config('snipcart.behaviour'),
            ]);
        });
    }

    /**
     * Returns the Snipcart API Key.
     *
     * @return mixed
     */
    protected function apiKey()
    {
        $mode = config('snipcart.test_mode');

        $apiKey = $mode
            ? config('snipcart.test_key')
            : config('snipcart.live_key');

        if (! $apiKey) {
            throw new ApiKeyNotFoundException($mode);
        }

        return $apiKey;
    }

    /**
     * Returns the currency of the current site.
     *
     * @return string
     */
    protected function currency(): string
    {
        return Currency::from(Site::current())->code();
    }
}
