<?php

namespace Aerni\Snipcart;

trait Attributes
{
   /**
     * All the mandatory Snipcart product attributes.
     *
     * @var array
     */
    protected static $requiredAttributes = ['name', 'id', 'price', 'url'];

    /**
     * All the optional Snipcart product attributes.
     *
     * @var array
     */
    protected static $optionalAttributes = [
        'description', 'image', 'categories', 'metadata', 'weight', 'length', 'height', 'width', 'quantity', 'max-quantity', 'min-quantity', 'stackable', 'quantity-step', 'shippable', 'taxable', 'taxes', 'has-taxes-included', 'file-guid',
    ];
}
