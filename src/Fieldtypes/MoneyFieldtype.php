<?php

namespace Aerni\Snipcart\Fieldtypes;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;
use Statamic\Fields\Fieldtype;

class MoneyFieldtype extends Fieldtype
{
    protected $icon = 'text';

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
     * @return string
     */
    public function preProcess($data)
    {
        return Currency::from(Site::current())->formatDecimal($data);
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return int
     */
    public function process($data)
    {
        return Currency::from(Site::current())->parseDecimal($data);
    }

    /**
     * Process the data before it gets loaded into the view.
     *
     * @param mixed $data
     * @return string
     */
    public function augment($data): string
    {
        return Currency::from(Site::current())->formatCurrency($data);
    }
}
