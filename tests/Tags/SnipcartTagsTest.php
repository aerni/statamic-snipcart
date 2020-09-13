<?php

namespace Aerni\Snipcart\Tests\Tags;

use Aerni\Snipcart\Facades\Config;
use Aerni\Snipcart\Tags\SnipcartTags;
use Aerni\Snipcart\Tests\TestCase;

class SnipcartTagsTest extends TestCase
{
    protected $tag;
    protected $config;

    public function setUp(): void
    {
        parent::setUp();

        $this->config = [
            'key' => Config::apiKey(),
            'currency' => Config::currency(),
            'version' => config('snipcart.version'),
            'behaviour' => config('snipcart.behaviour'),
        ];

        $this->tag = resolve(SnipcartTags::class);
    }

    /** @test */
    public function it_returns_the_snipcart_preconnect_hints()
    {
        $preconnectHints =
            "<link rel='preconnect' href='https://app.snipcart.com'>
            <link rel='preconnect' href='https://cdn.snipcart.com'>";

        $this->assertEquals($this->tag->preconnect(), $preconnectHints);
    }

    /** @test */
    public function it_returns_the_snipcart_stylesheets()
    {
        $version = $this->config['version'];
        $stylesheet = "<link rel='stylesheet' href='https://cdn.snipcart.com/themes/v{$version}/default/snipcart.css' />";

        $this->assertEquals($this->tag->stylesheet(), $stylesheet);
    }

    /** @test */
    public function it_returns_the_snipcart_container()
    {
        $key = $this->config['key'];
        $behaviour = $this->config['behaviour'];
        $currency = $this->config['currency'];

        $container =
            "<div hidden id='snipcart'
                data-api-key='{$key}'
                data-config-add-product-behavior='{$behaviour}'
                data-currency='{$currency}'>
            </div>";

        $this->assertEquals($this->tag->container(), $container);
    }

    /** @test */
    public function it_returns_the_snipcart_script()
    {
        $version = $this->config['version'];
        $script = "<script src='https://cdn.snipcart.com/themes/v{$version}/default/snipcart.js'></script>";

        $this->assertEquals($this->tag->script(), $script);
    }

    /** @test */
    public function it_returns_the_snipcart_head()
    {
        $version = $this->config['version'];

        $preconnectHints =
            "<link rel='preconnect' href='https://app.snipcart.com'>
            <link rel='preconnect' href='https://cdn.snipcart.com'>";

        $stylesheet = "<link rel='stylesheet' href='https://cdn.snipcart.com/themes/v{$version}/default/snipcart.css' />";

        $head = "{$preconnectHints} {$stylesheet}";

        $this->assertEquals($this->tag->head(), $head);
    }

    /** @test */
    public function it_returns_the_snipcart_body()
    {
        $key = $this->config['key'];
        $behaviour = $this->config['behaviour'];
        $currency = $this->config['currency'];
        $version = $this->config['version'];

        $container =
            "<div hidden id='snipcart'
                data-api-key='{$key}'
                data-config-add-product-behavior='{$behaviour}'
                data-currency='{$currency}'>
            </div>";

        $script = "<script src='https://cdn.snipcart.com/themes/v{$version}/default/snipcart.js'></script>";

        $body = "{$container} {$script}";

        $this->assertEquals($this->tag->body(), $body);
    }

    /** @test */
    public function it_returns_a_snipcart_buy_button()
    {
        //
    }

    /** @test */
    public function it_returns_the_snipcart_product_attributes()
    {
        //
    }

    /** @test */
    public function it_returns_a_snipcart_cart_button()
    {
        //
    }

    /** @test */
    public function it_returns_a_snipcart_signin_button()
    {
        //
    }

    /** @test */
    public function it_returns_the_number_of_cart_items()
    {
        //
    }

    /** @test */
    public function it_returns_the_total_cart_price()
    {
        //
    }
}
