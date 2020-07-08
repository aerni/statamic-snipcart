<?php

namespace Aerni\Snipcart;

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

    public function boot()
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'snipcart');
        
        $this->publishes([
            __DIR__.'/../config/snipcart.php' => config_path('snipcart.php'),
        ]);

        $this->mergeConfigFrom(__DIR__.'/../config/snipcart.php', 'snipcart');

        Statamic::booted(function () {
            $this->createNavigation();
        });
    }

    public function register()
    {
        parent::register();

        $this->app->bind(SnipcartTags::class, function () {
            $config = [
                'key' => config('snipcart.key'),
                'version' => config('snipcart.version'),
            ];

            return new SnipcartTags($config);
        });
    }

    protected function createNavigation()
    {
        Nav::extend(function ($nav) {
            $nav->create('Products')
                ->section('Snipcart')
                ->route('products.index')
                ->icon('drawer-file');
        });
    }
}
