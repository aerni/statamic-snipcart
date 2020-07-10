<?php

namespace Aerni\Snipcart;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Statamic\Entries\EntryCollection;
use Statamic\Facades\Asset;
use Statamic\Facades\Collection as StatamicCollection;
use Statamic\Facades\Entry;
use Statamic\Tags\Concerns;
use Statamic\Tags\Tags;

class SnipcartTags extends Tags
{
    use Concerns\OutputsItems;

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
     * All mandatory Snipcart product attributes.
     *
     * @var array
     */
    protected static $requiredAttributes = ['name', 'id', 'price', 'url'];

    /**
     * All optional Snipcart product attributes.
     *
     * @var array
     */
    protected static $optionalAttributes = ['description', 'image', 'categories', 'metadata', 'weight', 'length', 'height', 'width', 'quantity', 'max-quantity', 'min-quantity', 'stackable', 'quantity-step', 'shippable', 'taxable', 'taxes', 'has-taxes-included', 'file-guid'];

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
     * Loop through the Snipcart products collection.
     * {{ snipcart }} ... {{ /snipcart }}
     * 
     * @return EntryCollection
     */
    public function index(): EntryCollection
    {
        return $this->output(
            $this->products()->get()
        );
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
        return "<link rel='stylesheet' href='https://cdn.snipcart.com/themes/v{$this->config['version']}/default/snipcart.css' />";
    }

    /**
     * Return the Snipcart container.
     * {{ snipcart:container }}
     *
     * @return string
     */
    public function container(): string
    {
        return 
            "<div hidden id='snipcart' 
                data-api-key='{$this->config['key']}' 
                data-config-add-product-behavior='{$this->config['behaviour']}' 
                data-currency='{$this->config['currency']}'>
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
        return "<script src='https://cdn.snipcart.com/themes/v{$this->config['version']}/default/snipcart.js'></script>";
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
     * Return a Snipcart product button.
     * {{ snipcart:button }}
     *
     * @return string
     */
    public function button(): string
    {
        $class = $this->params->get('class');
        $dataAttributes = $this->dataAttributes();
        $text = $this->params->get('text') ?? __('snipcart::product.add_to_cart');

        return 
            "<button class='snipcart-add-item {$class}' {$dataAttributes}>
                {$text}
            </button>";
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
        $text = $this->params->get('text') ?? __('snipcart::product.show_cart');

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
        $text = $this->params->get('text') ?? __('snipcart::product.signin');

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
     * {{ snipcart:total }}
     *
     * @return string
     */
    public function total(): string
    {
        $class = $this->params->get('class');

        return "<span class='snipcart-total-price {$class}'></span>";
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

    /**
     * Get the Snipcart attributes.
     *
     * @return Collection
     */
    protected function attributes(): Collection
    {
        $attributes = $this->productAttributes()->merge($this->tagAttributes());
        dd($attributes);
        return $this->validateAttributes($attributes);
    }

    /**
     * Get the Snipcart attributes from the product entry.
     *
     * @return Collection
     */
    protected function productAttributes(): Collection
    {
        if (!is_null($this->currentEntry())) {

            $product = $this->currentEntry();
            $data = $product->data();

            $data->put('url', Request::url());
            $data->put('id', $product->id());
            
            return $this->transformAttributes($data);

        }

        return collect();
    }

    /**
     * Transform the attributes to match the format that Snipcart expects.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function transformAttributes(Collection $attributes): Collection
    {
        $transformedValues = $this->transformAttributeValues($attributes);
        $transformedKeys = $this->transformAttributeKeys($transformedValues);
        $validAttributes = $this->filterValidAttributes($transformedKeys);

        return $validAttributes;
    }

    /**
     * Transform the attribute values to match the format that Snipcart expects.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function transformAttributeValues(Collection $attributes): Collection
    {
        return $attributes->map(function ($item, $key) {

            if ($key === 'images' && is_array($item)) {
                return Asset::find("/assets/{$item[0]}")->url();
            }

            if ($key === 'categories' && is_array($item)) {
                return implode('|', $item);
            }

            if ($key === 'taxes' && is_array($item)) {
                return implode('|', $item);
            }
            
            if (Str::startsWith($key, 'custom') && is_array($item)) {
                return implode('|', $item);
            }

            if ($key === 'metadata' && is_array($item)) {
                return json_encode($item);
            }

            return $item;

        });
    }

    /**
     * Transform the attribute keys to match the format that Snipcart expects.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function transformAttributeKeys(Collection $attributes): Collection
    {   
        $transformedKeys = $attributes->mapWithKeys(function ($item, $key) {

            if ($key === 'title') {
                return ['name' => $item];
            }

            if ($key === 'images') {
                return ['image' => $item];
            }

            if (Str::startsWith($key, 'custom_')) {
                $hyphened = Str::of($key)->replace('_', '-');
                return [Str::of($hyphened)->replaceFirst('-', '')->__toString() => $item];
            }

            if (Str::contains($key, '_')) {
                return [Str::of($key)->replace('_', '-')->__toString() => $item];
            }

            return [$key => $item];

        });

        return $transformedKeys;

    }

    /**
     * Filter the attributes to only return valid attributes.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function filterValidAttributes(Collection $attributes): Collection
    {
        return $attributes->filter(function ($item, $key) {
            if ($this->isValidAttributeKey($key)) {
                return $item;
            }
        });
    }

    /**
     * Return true if the key is a valid Snipcart product attribute key.
     *
     * @param string $key
     * @return boolean
     */
    protected function isValidAttributeKey(string $key): bool
    {
        if (in_array($key, Self::$requiredAttributes)) {
            return true;
        }

        if (in_array($key, Self::$optionalAttributes)) {
            return true;
        }

        if (Str::startsWith($key, 'custom')) {
            return true;
        }

        return false;
    }

    /**
     * Get the Snipcart attributes from the tag.
     *
     * @return Collection
     */
    protected function tagAttributes(): Collection
    {
        return $this->params->except(['class', 'text']);
    }

    /**
     * Check if the attributes include Snipcart's mandatory product attributes.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function validateAttributes(Collection $attributes): Collection
    {
        if ($attributes->has(Self::$requiredAttributes)) {
            return $attributes;
        };

        throw new Exception("Please make sure that your products include the mandatory Snipcart attributes: [name], [id], [price], [url]");
    }

    /**
     * Get the products from the products collection.
     */
    protected function products()
    {
        return StatamicCollection::find('products')->queryEntries();
    }

    /**
     * Get the current entry.
     */
    protected function currentEntry()
    {
        return Entry::find($this->get('current', $this->context->get('id')));
    }
}