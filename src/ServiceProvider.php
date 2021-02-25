<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Facades\Config;
use Aerni\Snipcart\Tags\SnipcartTags;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        Commands\MigrateSnipcart::class,
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
        'web' => __DIR__ . '/../routes/web.php',
    ];

    protected $scripts = [
        __DIR__.'/../resources/dist/js/cp.js',
    ];

    protected $tags = [
        Tags\CurrencyTags::class,
        Tags\LengthTags::class,
        Tags\SnipcartTags::class,
        Tags\StockTags::class,
        Tags\WeightTags::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Statamic::booted(function () {
            $this->setSnipcartApiConfig();
            $this->setSnipcartWebhooksConfig();
        });

        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', [
                '--tag' => 'snipcart-config',
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
     * Set the config of the Snipcart API package.
     *
     * @return void
     */
    protected function setSnipcartApiConfig(): void
    {
        $snipcartApiConfig = config('snipcart-api', []);
        $snipcartConfig = config('snipcart', []);

        $mergedConfigs = array_intersect_key($snipcartConfig, $snipcartApiConfig);

        foreach ($mergedConfigs as $key => $value) {
            config()->set("snipcart-api.{$key}", $value);
        }
    }

    /**
     * Set the config of the Snipcart Webhooks package.
     *
     * @return void
     */
    protected function setSnipcartWebhooksConfig(): void
    {
        $snipcartWebhooksConfig = config('snipcart-webhooks', []);
        $snipcartConfig = config('snipcart', []);

        $mergedConfigs = array_intersect_key($snipcartConfig, $snipcartWebhooksConfig);

        foreach ($mergedConfigs as $key => $value) {
            config()->set("snipcart-webhooks.{$key}", $value);
        }
    }

    /**
     * Bind the repositories.
     *
     * @return void
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(\Statamic\Contracts\Entries\EntryRepository::class, Repositories\EntryRepository::class);
        $this->app->bind('Config', Repositories\ConfigRepository::class);
        $this->app->bind('Converter', Support\Converter::class);
        $this->app->bind('Currency', Repositories\CurrencyRepository::class);
        $this->app->bind('Dimension', Repositories\DimensionRepository::class);
        $this->app->bind('ProductApi', Repositories\ProductApiRepository::class);
        $this->app->bind('VariantsBuilder', Data\VariantsBuilder::class);
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
                'key' => Config::apiKey(),
                'currency' => Config::currency(),
                'version' => config('snipcart.version'),
                'behaviour' => config('snipcart.behaviour'),
            ]);
        });
    }
}
