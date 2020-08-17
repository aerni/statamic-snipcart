<?php

namespace Aerni\Snipcart\Fieldtypes;

use Statamic\Fields\Fieldtype;

class StockFieldtype extends Fieldtype
{
    protected $icon = 'tags';

    /**
     * Preload some data to be available in the vue component.
     *
     * @return array
     */
    public function preload()
    {
        //
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return $data;
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        return (int) $data;
    }
}
