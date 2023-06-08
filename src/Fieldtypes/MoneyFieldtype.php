<?php

namespace Aerni\Snipcart\Fieldtypes;

use Aerni\Snipcart\Facades\Currency;
use Statamic\Facades\Site;
use Statamic\Fields\Fieldtype;

class MoneyFieldtype extends Fieldtype
{
    /**
     * Preload some data to be available in the vue component.
     */
    public function preload(): array
    {
        return Currency::all();
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     */
    public function preProcess(mixed $data): ?string
    {
        return Currency::from(Site::current())->formatDecimal($data);
    }

    /**
     * Process the data before it gets saved.
     */
    public function process(mixed $data): int
    {
        return Currency::from(Site::current())->parseDecimal($data);
    }

    /**
     * Process the data before it gets loaded into the view.
     */
    public function augment(mixed $data): string
    {
        return Currency::from(Site::current())->formatCurrency($data);
    }
}
