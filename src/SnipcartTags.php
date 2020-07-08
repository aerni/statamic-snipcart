<?php

namespace Aerni\Snipcart;

use Illuminate\Support\Collection;
use Statamic\Tags\Tags;

class SnipcartTags extends Tags
{
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

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Return the Snipcart preconnect hints.
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
     *
     * @return string
     */
    public function stylesheet(): string
    {
        return "<link rel='stylesheet' href='https://cdn.snipcart.com/themes/v{$this->config['version']}/default/snipcart.css' />";
    }

    /**
     * Return the Snipcart container.
     *
     * @return string
     */
    public function container(): string
    {
        return 
            "<div hidden id='snipcart' 
                data-api-key='{$this->config['key']}' 
                data-config-add-product-behavior='{$this->params->get('behaviour')}' 
                data-currency='{$this->params->get('currency')}'>
            </div>";
    }

    /**
     * Return the Snipcart script.
     *
     * @return string
     */
    public function script(): string
    {
        return "<script src='https://cdn.snipcart.com/themes/v{$this->config['version']}/default/snipcart.js'></script>";
    }

    /**
     * Return the Snipcart preconnect hints and the stylesheet.
     *
     * @return string
     */
    public function head(): string
    {
        return "{$this->preconnect()} {$this->stylesheet()}";
    }

    /**
     * Return the Snipcart container and the script.
     *
     * @return string
     */
    public function body(): string
    {
        return "{$this->container()} {$this->script()}";    
    }

    /**
     * Return a Snipcart product button.
     *
     * @return string
     */
    public function product(): string
    {
        return 
            "<button class='snipcart-add-item {$this->params->get('class')}' {$this->dataAttributes()}>
                {$this->params->get('text')}
            </button>";
    }

    /**
     * Return a Snipcart cart button.
     *
     * @return string
     */
    public function cart(): string
    {        
        return 
            "<button class='snipcart-checkout {$this->params->get('class')}'>
                {$this->params->get('text')}
            </button>";
    }

    /**
     * Return a Snipcart customer signin button.
     *
     * @return string
     */
    public function signin(): string
    {
        return 
            "<button class='snipcart-customer-signin {$this->params->get('class')}'>
                {$this->params->get('text')}
            </button>";
    }

    /**
     * Return the number of items in the cart.
     *
     * @return string
     */
    public function items(): string
    {
        return "<span class='snipcart-items-count {$this->params->get('class')}'></span>";
    }

    /**
     * Return the total price of all the items in the cart.
     *
     * @return string
     */
    public function total(): string
    {
        return "<span class='snipcart-total-price {$this->params->get('class')}'></span>";
    }

    /**
     * Get the Snipcart attributes from the tag.
     *
     * @return Collection
     */
    protected function attributes(): Collection
    {
        return $this->params->except(['class', 'text']);
    }

    /**
     * Join all the Snipcart attributes to an HTML-ready string.
     *
     * @return string
     */
    protected function dataAttributes(): string
    {
        return $this->attributes()->map(function ($value, $key) {
            return "data-item-{$key}='{$value}'";
        })->implode(' ');
    }


}