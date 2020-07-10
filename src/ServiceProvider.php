<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Tags\SnipcartTags;
use Statamic\Facades\CP\Nav;
use Statamic\Statamic;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        SnipcartTags::class
    ];

    protected $routes = [
        'cp'  => __DIR__ . '/../routes/cp.php',
    ];

    public function boot(): void
    {
        parent::boot();

        $this->publishVendorStuff();

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

    protected function publishVendorStuff(): void
    {
        if ($this->app->runningInConsole()) {

            // Blueprints
            $this->publishes([
                __DIR__.'/../resources/blueprints' => resource_path('blueprints'),
            ]);

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
