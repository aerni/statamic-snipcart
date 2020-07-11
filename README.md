![GitHub release](https://flat.badgen.net/github/release/aerni/statamic-snipcart)
![Statamic](https://flat.badgen.net/badge/Statamic/3.0+/FF269E)

# Snipcart
This addon makes the setup of your Snipcart shop on Statamic a breeze.

⚙️ Simple installation and setup    
🛒 Automatically generated product attributes  
📋 Optimized blueprint with all the fields accepted by Snipcart

Read the official [Snipcart Documentation](https://docs.snipcart.com/v3/) for more information about how to setup your shop.

## Installation

### Step 1

Install the addon using Composer.

```bash
composer require aerni/statamic-snipcart
```

### Step 2

Run the installation command. It will guide you through the setup.

```bash
php please snipcart:install
```

### Step 3

Review the config published to `config/snipcart.php` and customize to your liking.

***

## Setup

### Step 1

Add this tag to the `<head>` of your view to render Snipcart's `preconnect hints` and `stylesheet`.

```template
{{ snipcart:head }}
```

If you want more control, you may add the `preconnect hints` and `stylesheet` separetely instead.

```template
{{ snipcart:preconnet }}
{{ snipcart:stylesheet }}
```

### Step 2

Add this tag before the closing `</body>` tag of your view to render Snipcart's `container` and `script`.

```template
{{ snipcart:body }}
```

If you want more control, you may add the `container` and `script` separetely instead. Make sure to include the `script` after the `container`.

```template
{{ snipcart:container }}
{{ snipcart:script }}
```

***

## Basic Usage

The products are stored in a regular Statamic collection. You can access the product data using Statamic's `{{ collection }}` tag with all its bells and whistles. Nothing fancy here.

```template
{{ collection:products }}
    {{ title }}
{{ /collection:products }}
```

***

## Tags

There's a couple of useful tags to render Snipcart specific HTML elements with all the necesarry attributes to make them work.

### Product Button

This tag will output a Snipcart product button. The required `data-item-*` attributes are generated based on the fields in the product's `.md` file.

```template
{{ snipcart:button }}
```

You may override any attribute directly on the tag.

```template
{{ snipcart:button id="{{ increment }}" name="{{ some_variable }}" }}
```

>**Note:** If you use this tag outside of a collection loop, you'll have to manually define the attributes.

### Cart Button

This tag will output a Snipcart cart button.

```template
{{ snipcart:cart }}
```

### Signin Button

This tag will output a Snipcart signin button.

```template
{{ snipcart:signin }}
```

### Items Count

This tag will output the number of items in the cart.

```template
{{ snipcart:items }}
```

### Total Price

This tag will output the total price of all the items in the cart.

```template
{{ snipcart:total }}
```

### Optional Parameters

There's a couple of optional parameters you may use on the tags.

| Parameter | Description | Supported by Tag |
|-----------|-------------|--------------|
`class` | Add additional classes to the HTML element | `button`, `cart`, `signin`, `items`, `total`
`text`  | Override the default text of the HTML element | `button`, `cart`, `signin`

```template
{{ snipcart:cart class="p-2 bg-gray-100" text="Checkout" }}
```

***

## Custom Fields
To add a custom attribute, create a new field in the blueprint and set the handle to `custom_{number}_{type}`e.g. `custom_1_name`. This will make the custom attributes available in the `{{ snipcart:button }}` tag.

***

## Customize Default Text

You may customize the default text of buttons in the language files located in `resources/lang/vendor/snipcart`.
