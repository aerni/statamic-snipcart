<?php

namespace Aerni\Snipcart;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $tags = [
        SnipcartTags::class
    ];

    public function boot()
    {
        parent::boot();

        $this->mergeConfigFrom(__DIR__.'/../config/snipcart.php', 'snipcart');

        $this->publishes([
            __DIR__.'/../config/snipcart.php' => config_path('snipcart.php'),
        ]);
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
}
