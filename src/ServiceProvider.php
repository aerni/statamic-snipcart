<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Actions\GetProductId;
use Aerni\Snipcart\Actions\GetProductStock;
use Aerni\Snipcart\Actions\GetProductVariants;
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
        Fieldtypes\MoneyFieldtype::class,
    ];

    protected $listen = [
        'Aerni\SnipcartWebhooks\Events\OrderCompleted' => [
            'Aerni\Snipcart\Listeners\ClearProductApiCache',
        ],
    ];

    protected $modifiers = [
        Modifiers\AddOperator::class,
        Modifiers\StripUnit::class,
    ];

    protected $routes = [
        'web' => __DIR__.'/../routes/web.php',
    ];

    protected $tags = [
        Tags\CurrencyTags::class,
        Tags\LengthTags::class,
        Tags\SnipcartTags::class,
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

        Collection::computed($collection, 'sku', fn ($entry) => GetProductId::handle($entry));
        Collection::computed($collection, 'stock', fn ($entry) => GetProductStock::handle($entry));
        Collection::computed($collection, 'variants', fn ($entry) => GetProductVariants::handle($entry));
    }
}
