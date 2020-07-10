<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Commands\InstallSnipcart;
use Aerni\Snipcart\Tags\SnipcartTags;
use Statamic\Facades\CP\Nav;
use Statamic\Statamic;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        InstallSnipcart::class,
    ];

    protected $tags = [
        SnipcartTags::class
    ];

    protected $routes = [
        'cp'  => __DIR__ . '/../routes/cp.php',
    ];

    public function boot(): void
    {
        parent::boot();

        $this->publishVendorFiles();

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'snipcart');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'snipcart');
        $this->mergeConfigFrom(__DIR__.'/../config/snipcart.php', 'snipcart');

        $this->createNavigation();

    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(SnipcartTags::class, function () {
            return new SnipcartTags(config('snipcart'));
        });
    }

    protected function createNavigation(): void
    {
        Nav::extend(function ($nav) {
            $nav->create('Products')
                ->section('Snipcart')
                ->route('products.index')
                ->icon('drawer-file');
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
