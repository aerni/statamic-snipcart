<?php

namespace Aerni\Snipcart\Fieldtypes;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Fields\Fieldtype;

class CurrencyFieldtype extends Fieldtype
{
    protected $icon = 'tags';

    /**
     * Preload some data to be available in the vue component.
     *
     * @return array
     */
    public function preload(): array
    {
        return Currency::all();
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return Currency::parse($data);
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        return Currency::parse($data);
    }
}
