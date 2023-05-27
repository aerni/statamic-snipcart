<?php

namespace Aerni\Snipcart\Tags;

use Statamic\Tags\Tags;
use Illuminate\Support\Str;
use Aerni\Snipcart\Facades\Config;
use Aerni\Snipcart\Tags\Concerns\GetsProductAttributes;

class SnipcartTags extends Tags
{
    use GetsProductAttributes;

    protected static $handle = 'snipcart';

    protected function settings(): string
    {
        $defaultConfig = [
            'publicApiKey' => Config::apiKey(),
            'currency' => Config::currency(),
        ];

        $userConfig = config('snipcart.snipcart_settings', []);

        return collect($defaultConfig)->merge($userConfig)->toJson();
    }

    /**
     * Returns the Snipcart script.
     * {{ snipcart:script }}
     */
    public function script(): string
    {
        return <<<EOD
            <script>
                window.SnipcartSettings = {$this->settings()};

                (function(){var c,d;(d=(c=window.SnipcartSettings).version)!=null||(c.version="3.0");var s,S;(S=(s=window.SnipcartSettings).timeoutDuration)!=null||(s.timeoutDuration=2750);var l,p;(p=(l=window.SnipcartSettings).domain)!=null||(l.domain="cdn.snipcart.com");var w,u;(u=(w=window.SnipcartSettings).protocol)!=null||(w.protocol="https");var m,g;(g=(m=window.SnipcartSettings).loadCSS)!=null||(m.loadCSS=!0);var y=window.SnipcartSettings.version.includes("v3.0.0-ci")||window.SnipcartSettings.version!="3.0"&&window.SnipcartSettings.version.localeCompare("3.4.0",void 0,{numeric:!0,sensitivity:"base"})===-1,f=["focus","mouseover","touchmove","scroll","keydown"];window.LoadSnipcart=o;document.readyState==="loading"?document.addEventListener("DOMContentLoaded",r):r();function r(){window.SnipcartSettings.loadStrategy?window.SnipcartSettings.loadStrategy==="on-user-interaction"&&(f.forEach(function(t){return document.addEventListener(t,o)}),setTimeout(o,window.SnipcartSettings.timeoutDuration)):o()}var a=!1;function o(){if(a)return;a=!0;let t=document.getElementsByTagName("head")[0],n=document.querySelector("#snipcart"),i=document.querySelector('src[src^="'.concat(window.SnipcartSettings.protocol,"://").concat(window.SnipcartSettings.domain,'"][src$="snipcart.js"]')),e=document.querySelector('link[href^="'.concat(window.SnipcartSettings.protocol,"://").concat(window.SnipcartSettings.domain,'"][href$="snipcart.css"]'));n||(n=document.createElement("div"),n.id="snipcart",n.setAttribute("hidden","true"),document.body.appendChild(n)),h(n),i||(i=document.createElement("script"),i.src="".concat(window.SnipcartSettings.protocol,"://").concat(window.SnipcartSettings.domain,"/themes/v").concat(window.SnipcartSettings.version,"/default/snipcart.js"),i.async=!0,t.appendChild(i)),!e&&window.SnipcartSettings.loadCSS&&(e=document.createElement("link"),e.rel="stylesheet",e.type="text/css",e.href="".concat(window.SnipcartSettings.protocol,"://").concat(window.SnipcartSettings.domain,"/themes/v").concat(window.SnipcartSettings.version,"/default/snipcart.css"),t.prepend(e)),f.forEach(function(v){return document.removeEventListener(v,o)})}function h(t){!y||(t.dataset.apiKey=window.SnipcartSettings.publicApiKey,window.SnipcartSettings.addProductBehavior&&(t.dataset.configAddProductBehavior=window.SnipcartSettings.addProductBehavior),window.SnipcartSettings.modalStyle&&(t.dataset.configModalStyle=window.SnipcartSettings.modalStyle),window.SnipcartSettings.currency&&(t.dataset.currency=window.SnipcartSettings.currency),window.SnipcartSettings.templatesUrl&&(t.dataset.templatesUrl=window.SnipcartSettings.templatesUrl))}})();
            </script>
        EOD;
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
