<?php

namespace Aerni\Snipcart\Fieldtypes;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;
use Statamic\Fields\Fieldtype;

class MoneyFieldtype extends Fieldtype
{
    protected $icon = 'tags';

    /**
     * Preload some data to be available in the vue component.
     *
     * @return array
     */
    public function preload(): array
    {
        return Currency::get(Site::current());
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return string|null
     */
    public function preProcess($data)
    {
        return Currency::formatDecimalIntl($data, Site::current());
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return int|null
     */
    public function process($data)
    {
        return Currency::parseDecimal($data, Site::current());
    }

    /**
     * Process the data before it gets loaded into the view.
     *
     * @param mixed $data
     * @return string|null
     */
    public function augment($data)
    {
        return Currency::formatDecimalIntl($data, Site::current());
    }
}
