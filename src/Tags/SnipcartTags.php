<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Tags\Concerns\GetsProductAttributes;
use Statamic\Tags\Tags;

class SnipcartTags extends Tags
{
    use GetsProductAttributes;

    /**
     * The handle of the tag.
     *
     * @var string
     */
    protected static $handle = 'snipcart';

    /**
     * An alias of the tag handle.
     *
     * @var array
     */
    protected static $aliases = ['sc'];

    /**
     * The config of this addon.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Construct the class.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Return the Snipcart preconnect hints.
     * {{ snipcart:preconnect }}
     *
     * @return string
     */
    public function preconnect(): string
    {
        return
            "<link rel='preconnect' href='https://app.snipcart.com'>
            <link rel='preconnect' href='https://cdn.snipcart.com'>";
    }

    /**
     * Return the Snipcart stylesheet.
     * {{ snipcart:stylesheet }}
     *
     * @return string
     */
    public function stylesheet(): string
    {
        $version = $this->config['version'];

        return "<link rel='stylesheet' href='https://cdn.snipcart.com/themes/v{$version}/default/snipcart.css' />";
    }

    /**
     * Return the Snipcart container.
     * {{ snipcart:container }}
     *
     * @return string
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
     * Return the Snipcart script.
     * {{ snipcart:script }}
     *
     * @return string
     */
    public function script(): string
    {
        $version = $this->config['version'];

        return "<script src='https://cdn.snipcart.com/themes/v{$version}/default/snipcart.js'></script>";
    }

    /**
     * Return the Snipcart preconnect hints and the stylesheet.
     * {{ snipcart:head }}
     *
     * @return string
     */
    public function head(): string
    {
        return "{$this->preconnect()} {$this->stylesheet()}";
    }

    /**
     * Return the Snipcart container and the script.
     * {{ snipcart:body }}
     *
     * @return string
     */
    public function body(): string
    {
        return "{$this->container()} {$this->script()}";
    }

    /**
     * Return a simple Snipcart product buy button.
     * {{ snipcart:buy }}
     *
     * @return string
     */
    public function buy(): string
    {
        $attributes = $this->dataAttributes();
        $class = $this->params->get('class');
        $text = $this->params->get('text') ?? __('snipcart::buttons.add_to_cart');

        return
            "<button class='snipcart-add-item {$class}' {$attributes}>
                {$text}
            </button>";
    }

    /**
     * Return the product attributes.
     * {{ snipcart:attributes }}
     *
     * @return string
     */
    public function attributes(): string
    {
        return $this->dataAttributes();
    }

    /**
     * Return a Snipcart cart button.
     * {{ snipcart:cart }}
     *
     * @return string
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
     * Return a Snipcart customer signin button.
     * {{ snipcart:signin }}
     *
     * @return string
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
     * Return the number of items in the cart.
     * {{ snipcart:items }}
     *
     * @return string
     */
    public function items(): string
    {
        $class = $this->params->get('class');
        
        return "<span class='snipcart-items-count {$class}'></span>";
    }

    /**
     * Return the total price of all the items in the cart.
     * {{ snipcart:price }}
     *
     * @return string
     */
    public function price(): string
    {
        $class = $this->params->get('class');

        return "<span class='snipcart-total-price {$class}'></span>";
    }
}
