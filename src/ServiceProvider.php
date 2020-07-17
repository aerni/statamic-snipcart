<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Commands\InstallSnipcart;
use Aerni\Snipcart\Fieldtypes\MoneyFieldtype;
use Aerni\Snipcart\Repositories\CurrencyRepository;
use Aerni\Snipcart\Tags\SnipcartTags;
use Aerni\Snipcart\Tags\CurrencyTags;
use Statamic\Statamic;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        InstallSnipcart::class,
    ];

    protected $fieldtypes = [
        MoneyFieldtype::class,
    ];

    protected $scripts = [
        __DIR__.'/../resources/dist/js/cp.js',
    ];

    protected $tags = [
        CurrencyTags::class,
        SnipcartTags::class,
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
            return new SnipcartTags(config('snipcart'));
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

        return $this;
    }
}
