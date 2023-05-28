<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Facades\VariantsBuilder;
use Illuminate\Support\Facades\Cache;
use Statamic\Facades\Collection;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        Commands\SetupSnipcart::class,
        Commands\SyncSites::class,
    ];

    protected $fieldtypes = [
        Fieldtypes\DimensionFieldtype::class,
        Fieldtypes\StockFieldtype::class,
        Fieldtypes\MoneyFieldtype::class,
    ];

    protected $listen = [
        'Aerni\SnipcartWebhooks\Events\OrderCompleted' => [
            'Aerni\Snipcart\Listeners\ClearProductApiCache',
        ],
        'Statamic\Events\EntryBlueprintFound' => [
            'Aerni\Snipcart\Listeners\MakeSkuReadOnly',
        ],
        'Statamic\Events\EntrySaving' => [
            'Aerni\Snipcart\Listeners\BuildProductVariants',
        ],
    ];

    protected $modifiers = [
        Modifiers\AddOperator::class,
        Modifiers\FormatPrice::class,
        Modifiers\StripUnit::class,
    ];

    protected $routes = [
        'web' => __DIR__.'/../routes/web.php',
    ];

    protected $tags = [
        Tags\CurrencyTags::class,
        Tags\LengthTags::class,
        Tags\SnipcartTags::class,
        Tags\StockTags::class,
        Tags\WeightTags::class,
    ];

    protected $vite = [
        'input' => [
            'resources/js/cp.js',
        ],
        'publicDirectory' => 'resources/dist',
        'hotFile' => __DIR__.'/../resources/dist/hot',
    ];

    public function bootAddon(): void
    {
        $this->registerSnipcartApiConfig();
        $this->registerSnipcartWebhooksConfig();
        $this->registerComputedValues();

        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', [
                '--tag' => 'snipcart-config',
            ]);
            $command->call('snipcart:sync-sites');
        });
    }

    public function register(): void
    {
        $this->app->bind('Config', Repositories\ConfigRepository::class);
        $this->app->bind('Converter', Support\Converter::class);
        $this->app->bind('Currency', Repositories\CurrencyRepository::class);
        $this->app->bind('Dimension', Repositories\DimensionRepository::class);
        $this->app->bind('ProductApi', Repositories\ProductApiRepository::class);
        $this->app->bind('VariantsBuilder', Data\VariantsBuilder::class);
    }

    /**
     * Register the config of the Snipcart API package.
     */
    protected function registerSnipcartApiConfig(): void
    {
        $snipcartApiConfig = config('snipcart-api', []);
        $snipcartConfig = config('snipcart', []);

        $mergedConfigs = array_intersect_key($snipcartConfig, $snipcartApiConfig);

        foreach ($mergedConfigs as $key => $value) {
            config()->set("snipcart-api.{$key}", $value);
        }
    }

    /**
     * Register the config of the Snipcart Webhooks package.
     */
    protected function registerSnipcartWebhooksConfig(): void
    {
        $snipcartWebhooksConfig = config('snipcart-webhooks', []);
        $snipcartConfig = config('snipcart', []);

        $mergedConfigs = array_intersect_key($snipcartConfig, $snipcartWebhooksConfig);

        foreach ($mergedConfigs as $key => $value) {
            config()->set("snipcart-webhooks.{$key}", $value);
        }
    }

    protected function registerComputedValues(): void
    {
        $collection = config('snipcart.products.collection');

        Collection::computed($collection, 'variants', function ($entry, $value) {
            return Cache::rememberForever("variants::{$entry->id()}", function () use ($entry) {
                return ! empty($entry->get('variations')) ? VariantsBuilder::process($entry) : null;
            });
        });
    }
}
