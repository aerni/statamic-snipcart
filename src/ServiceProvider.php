<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Commands\SetupSnipcart;
use Aerni\Snipcart\Fieldtypes\CurrencyFieldtype;
use Aerni\Snipcart\Fieldtypes\LengthFieldtype;
use Aerni\Snipcart\Fieldtypes\WeightFieldtype;
use Aerni\Snipcart\Repositories\CurrencyRepository;
use Aerni\Snipcart\Repositories\LengthRepository;
use Aerni\Snipcart\Repositories\ProductRepository;
use Aerni\Snipcart\Repositories\WeightRepository;
use Aerni\Snipcart\Tags\CurrencyTags;
use Aerni\Snipcart\Tags\LengthTags;
use Aerni\Snipcart\Tags\SnipcartTags;
use Aerni\Snipcart\Tags\WeightTags;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        SetupSnipcart::class,
    ];

    protected $fieldtypes = [
        CurrencyFieldtype::class,
        LengthFieldtype::class,
        WeightFieldtype::class,
    ];

    protected $scripts = [
        __DIR__.'/../resources/dist/js/cp.js',
    ];

    protected $tags = [
        CurrencyTags::class,
        LengthTags::class,
        SnipcartTags::class,
        WeightTags::class,
    ];

    public function boot(): void
    {
        parent::boot();

        $this->publishVendorFiles();

        $this->mergeConfigFrom(__DIR__.'/../config/snipcart.php', 'snipcart');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'snipcart');

        Statamic::booted(function () {
            $this->bindRepositories();
        });
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(SnipcartTags::class, function () {
            return new SnipcartTags([
                'key' => $this->apiKey(),
                'currency' => config('snipcart.currency'),
                'version' => config('snipcart.version'),
                'behaviour' => config('snipcart.behaviour'),
            ]);
        });
    }

    protected function publishVendorFiles(): void
    {
        if ($this->app->runningInConsole()) {

            // Config
            $this->publishes([
                __DIR__.'/../config/snipcart.php' => config_path('snipcart.php'),
            ]);

            // Lang
            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/snipcart'),
            ]);
        }
    }

    protected function bindRepositories()
    {
        $this->app->bind('Currency', CurrencyRepository::class);
        $this->app->bind('Length', LengthRepository::class);
        $this->app->bind('Product', ProductRepository::class);
        $this->app->bind('Weight', WeightRepository::class);

        return $this;
    }

    /**
     * Get the Snipcart API Key from the config.
     *
     * @return string
     */
    protected function apiKey(): string
    {
        if (config('snipcart.test_mode')) {
            return config('snipcart.test_key');
        }

        return config('snipcart.live_key');
    }
}
