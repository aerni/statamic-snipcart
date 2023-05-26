<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Tags\Concerns\GetsProductAttributes;
use Statamic\Tags\Tags;

class SnipcartTags extends Tags
{
    use GetsProductAttributes;

    protected static $handle = 'snipcart';

    public function __construct(protected array $config)
    {
        //
    }

    /**
     * Returns the Snipcart preconnect hints.
     * {{ snipcart:preconnect }}
     */
    public function preconnect(): string
    {
        return
            "<link rel='preconnect' href='https://app.snipcart.com'>
            <link rel='preconnect' href='https://cdn.snipcart.com'>";
    }

    /**
     * Returns the Snipcart stylesheet.
     * {{ snipcart:stylesheet }}
     */
    public function stylesheet(): string
    {
        $version = $this->config['version'];

        return "<link rel='stylesheet' href='https://cdn.snipcart.com/themes/v{$version}/default/snipcart.css' />";
    }

    /**
     * Returns the Snipcart container.
     * {{ snipcart:container }}
     */
    public function container(): string
    {
        $key = $this->config['key'];
        $behaviour = $this->config['behaviour'];
        $currency = $this->config['currency'];

        return
            "<div hidden id='snipcart'
                data-api-key='{$key}'
                data-config-add-product-behavior='{$behaviour}'
                data-currency='{$currency}'>
            </div>";
    }

    /**
     * Returns the Snipcart script.
     * {{ snipcart:script }}
     */
    public function script(): string
    {
        $version = $this->config['version'];

        return "<script src='https://cdn.snipcart.com/themes/v{$version}/default/snipcart.js'></script>";
    }

    /**
     * Returns the Snipcart preconnect hints and the stylesheet.
     * {{ snipcart:head }}
     */
    public function head(): string
    {
        return "{$this->preconnect()} {$this->stylesheet()}";
    }

    /**
     * Returns the Snipcart container and the script.
     * {{ snipcart:body }}
     */
    public function body(): string
    {
        return "{$this->container()} {$this->script()}";
    }

    /**
     * Returns a simple Snipcart product buy button.
     * {{ snipcart:buy }}
     */
    public function buy(): string
    {
        $attributes = $this->productAttributes();
        $class = $this->params->get('class');
        $text = $this->params->get('text') ?? __('snipcart::buttons.add_to_cart');

        return
            "<button class='snipcart-add-item {$class}' {$attributes}>
                {$text}
            </button>";
    }

    /**
     * Returns the Snipcart product attributes.
     * {{ snipcart:attributes }}
     */
    public function attributes(): string
    {
        return $this->productAttributes();
    }

    /**
     * Returns a Snipcart cart button.
     * {{ snipcart:cart }}
     */
    public function cart(): string
    {
        $class = $this->params->get('class');
        $text = $this->params->get('text') ?? __('snipcart::buttons.show_cart');

        return
            "<button class='snipcart-checkout {$class}'>
                {$text}
            </button>";
    }

    /**
     * Returns a Snipcart customer signin button.
     * {{ snipcart:signin }}
     */
    public function signin(): string
    {
        $class = $this->params->get('class');
        $text = $this->params->get('text') ?? __('snipcart::buttons.signin');

        return
            "<button class='snipcart-customer-signin {$class}'>
                {$text}
            </button>";
    }

    /**
     * Returns the number of items in the cart.
     * {{ snipcart:items }}
     */
    public function items(): string
    {
        $class = $this->params->get('class');

        return "<span class='snipcart-items-count {$class}'></span>";
    }

    /**
     * Returns the total price of all the items in the cart.
     * {{ snipcart:price }}
     */
    public function price(): string
    {
        $class = $this->params->get('class');

        return "<span class='snipcart-total-price {$class}'></span>";
    }
}
