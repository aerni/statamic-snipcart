<?php

namespace Aerni\Snipcart;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->mergeConfigFrom(__DIR__.'/../config/snipcart.php', 'snipcart');

        $this->publishes([
            __DIR__.'/../config/snipcart.php' => config_path('snipcart.php'),
        ]);
    }
}
