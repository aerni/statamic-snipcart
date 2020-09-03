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
     * @param int|null $data
     * @return string|null
     */
    public function preProcess($data)
    {
        return Currency::from(Site::current())->formatDecimalIntl($data);
    }

    /**
     * Process the data before it gets saved.
     *
     * @param string|null $data
     * @return int|null
     */
    public function process($data)
    {
        return Currency::from(Site::current())->parseDecimalIntl($data);
    }

    /**
     * Process the data before it gets loaded into the view.
     *
     * @param int|null $data
     * @return string|null
     */
    public function augment($data)
    {
        return Currency::from(Site::current())->formatCurrency($data);
    }
}
