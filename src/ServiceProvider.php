<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Tags\SnipcartTags;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{   
    protected $commands = [
        Commands\SetupSnipcart::class,
    ];

    protected $fieldtypes = [
        Fieldtypes\CurrencyFieldtype::class,
        Fieldtypes\LengthFieldtype::class,
        Fieldtypes\WeightFieldtype::class,
    ];

    protected $listen = [
        'Statamic\Events\EntryBlueprintFound' => [
            'Aerni\Snipcart\Listeners\EditingProduct',
        ],
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
        });

        Statamic::afterInstalled(function ($command) {
            $command->call('snipcart:setup');
            $command->call('vendor:publish', [ 
                '--provider' => 'Aerni\Snipcart\ServiceProvider',
            ]);
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
     * Bind the repositories.
     *
     * @return void
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(\Statamic\Contracts\Entries\EntryRepository::class, Repositories\EntryRepository::class);
        $this->app->bind('Currency', Repositories\CurrencyRepository::class);
        $this->app->bind('Length', Repositories\LengthRepository::class);
        $this->app->bind('Product', Repositories\ProductRepository::class);
        $this->app->bind('Weight', Repositories\WeightRepository::class);
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
                'key' => config('snipcart.test_mode') ? config('snipcart.test_key') : config('snipcart.live_key'),
                'currency' => config('snipcart.currency'),
                'version' => config('snipcart.version'),
                'behaviour' => config('snipcart.behaviour'),
            ]);
        });
    }
}
