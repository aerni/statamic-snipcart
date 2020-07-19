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

Run the setup command. It will guide you through the setup.

```bash
php please snipcart:setup
```

### Step 3

Add your Snipcart API Key to your .env file.

```env
SNIPCART_API_KEY=************************************************************************
```

### Step 4

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

## Snipcart Tags

There are a couple of useful tags to render Snipcart specific HTML elements with all the necesarry attributes to make them work.

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
{{ snipcart:price }}
```

### Optional Parameters

There's a couple of optional parameters you may use on the tags.

| Parameter | Description | Supported by Tag |
|-----------|-------------|--------------|
`class` | Add additional classes to the HTML element | `button`, `cart`, `signin`, `items`, `price`
`text`  | Override the default text of the HTML element | `button`, `cart`, `signin`

```template
{{ snipcart:cart class="p-2 bg-gray-100" text="Checkout" }}
```

***

## Currency Tags

There are a handful of tags to get currency related data of the `currency` defined in the config.

```template
{{ currency:code }} // e.g. 'USD'
{{ currency:name }} e.g. 'US Dollar'
{{ currency:symbol }} // e.g. '$'
```

You may also access this information with a tag pair.

```template
{{ currency }}
    {{ code }} {{ name }} {{ symbol }}
{{ /currency }}
```

***

## Length Tags

There are a handful of tags to get length related data of the `length` defined in the config.

```template
{{ length:abbr }} // e.g. 'cm'
{{ length:singular }} e.g. 'Centimeter'
{{ length:plural }} // e.g. 'Centimeters'
```

You may also access this information with a tag pair.

```template
{{ length }}
    {{ abbr }} {{ singular }} {{ plural }}
{{ /length }}
```

***

## Weight Tags

There are a handful of tags to get weight related data of the `weight` defined in the config.

```template
{{ weight:abbr }} // e.g. 'kg'
{{ weight:singular }} e.g. 'Kilogram'
{{ weight:plural }} // e.g. 'Kilograms'
```

You may also access this information with a tag pair.

```template
{{ weight }}
    {{ abbr }} {{ singular }} {{ plural }}
{{ /weight }}
```

***

## Customize Blueprint

Feel free to add your own fields to the generated blueprints. This might be useful if you want to add more content about a product.
>**Important:** Make sure to `NOT` change any handles of existing fields. If you do, things will blow up.

### Image Asset Container

The assets container for the images defaults to `assets`. You may change the asset container to your liking.

***

## Default Button Text

You may customize the default text of buttons in the language files located in `resources/lang/vendor/snipcart`.
