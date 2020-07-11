<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Commands\InstallSnipcart;
use Aerni\Snipcart\Tags\SnipcartTags;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        InstallSnipcart::class,
    ];

    protected $tags = [
        SnipcartTags::class
    ];

    public function boot(): void
    {
        parent::boot();

        $this->publishVendorFiles();

        $this->mergeConfigFrom(__DIR__.'/../config/snipcart.php', 'snipcart');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'snipcart');

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
}
