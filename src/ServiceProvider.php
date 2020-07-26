<?php

namespace Aerni\Snipcart;

use Aerni\Snipcart\Blueprints\Blueprint;
use Aerni\Snipcart\Tags\SnipcartTags;
use Illuminate\Support\Facades\Cache;
use Statamic\Facades\Blueprint as StatamicBlueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\Taxonomy;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;
use Statamic\Support\Str;

class ServiceProvider extends AddonServiceProvider
{
    protected $publishAfterInstall = false;
    
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

        $this->publishVendorFiles();

        $this->mergeConfigFrom(__DIR__.'/../config/snipcart.php', 'snipcart');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'snipcart');

        Statamic::booted(function () {
            $this->setupContent();
        });

        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', [ 
                '--provider' => 'Aerni\Snipcart\ServiceProvider',
                '--force' => true,
            ]);
        });
    }

    public function register(): void
    {
        parent::register();

        Statamic::booted(function () {
            $this->bindRepositories();
        });

        $this->app->bind(SnipcartTags::class, function () {
            return new SnipcartTags([
                'key' => $this->apiKey(),
                'currency' => config('snipcart.currency'),
                'version' => config('snipcart.version'),
                'behaviour' => config('snipcart.behaviour'),
            ]);
        });
    }

    /**
     * Publish the vendor files.
     *
     * @return void
     */
    protected function publishVendorFiles(): void
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../config/snipcart.php' => config_path('snipcart.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/snipcart'),
            ], 'lang');

        }
    }

    /**
     * Setup the product collections and taxonomies.
     *
     * @return void
     */
    protected function setupContent(): void
    {
        $products = config('snipcart.collections.products');
        $categories = config('snipcart.taxonomies.categories');
        $taxes = config('snipcart.taxonomies.taxes');

        if (! Collection::handleExists($products)) {
            Collection::make($products)
                ->title(Str::studlyToTitle($products))
                ->pastDateBehavior('public')
                ->futureDateBehavior('private')
                ->routes('/' . Str::slug(Str::studlyToTitle($products)) . '/{slug}')
                ->taxonomies([$categories])
                ->save();
        }

        if (! StatamicBlueprint::find("collections/{$products}/product")) {
            (new Blueprint())
                ->parse("collections/products/product.yaml")
                ->make('product')
                ->namespace("collections.{$products}")
                ->save();
        }

        if (! Taxonomy::handleExists($categories)) {
            Taxonomy::make($categories)
                ->title(Str::studlyToTitle($categories))
                ->save();
        }

        if (! StatamicBlueprint::find("taxonomies/{$categories}/category")) {
            (new Blueprint())
                ->parse("taxonomies/categories/category.yaml")
                ->make('category')
                ->namespace("taxonomies.{$categories}")
                ->save();
        }

        if (! Taxonomy::handleExists($taxes)) {
            Taxonomy::make($taxes)
                ->title(Str::studlyToTitle($taxes))
                ->save();
        }

        if (! StatamicBlueprint::find("taxonomies/{$taxes}/tax")) {
            (new Blueprint())
                ->parse("taxonomies/taxes/tax.yaml")
                ->make('tax')
                ->namespace("taxonomies.{$taxes}")
                ->save();
        }

        if ($this->hasNewHandle($products, $categories, $taxes)) {
            $this->updateProductBlueprint($products, $categories, $taxes);

            Cache::put('products', $products);
            Cache::put('categories', $categories);
            Cache::put('taxes', $taxes);
        }
    }

    /**
     * Update the product blueprint with the categories and taxes taxonomies.
     *
     * @param string $products
     * @param string $categories
     * @param string $taxes
     * @return void
     */
    protected function updateProductBlueprint(string $products, string $categories, string $taxes): void
    {
        $blueprint = StatamicBlueprint::find("collections/{$products}/product");

        $content = $blueprint->contents();

        $content['sections']['advanced']['fields'][1]['handle'] = $categories;
        $content['sections']['advanced']['fields'][1]['field']['taxonomy'] = $categories;
        $content['sections']['advanced']['fields'][13]['handle'] = $taxes;
        $content['sections']['advanced']['fields'][13]['field']['taxonomy'] = $taxes;

        $blueprint->setContents($content)->save();
    }

    /**
     * Bind the repositories.
     *
     * @return void
     */
    protected function bindRepositories(): void
    {
        $this->app->bind(\Statamic\Contracts\Entries\EntryRepository::class, Repositories\EntryRepository::class);
        $this->app->bind('Currency', Repositories\CurrencyRepository::class);
        $this->app->bind('Length', Repositories\LengthRepository::class);
        $this->app->bind('Product', Repositories\ProductRepository::class);
        $this->app->bind('Weight', Repositories\WeightRepository::class);
    }

    /**
     * Get the Snipcart API Key from the config.
     *
     * @return string
     */
    protected function apiKey(): string
    {
        if (config('snipcart.test_mode')) {
            return config('snipcart.test_key');
        }

        return config('snipcart.live_key');
    }

    /**
     * Check if the handle from the config is different to the handle in the cache.
     *
     * @param string $products
     * @param string $categories
     * @param string $taxes
     * @return bool
     */
    protected function hasNewHandle(string $products, string $categories, string $taxes): bool
    {
        if (Cache::get('products') !== $products) {
            return true;
        }

        if (Cache::get('categories') !== $categories) {
            return true;
        }

        if (Cache::get('taxes') !== $taxes) {
            return true;
        }

        return false;
    }
}
