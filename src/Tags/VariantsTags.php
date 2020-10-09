<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\VariantsBuilder;
use Statamic\Tags\Tags;

class VariantsTags extends Tags
{
    /**
     * The handle of the tag.
     *
     * @var string
     */
    protected static $handle = 'variants';

    /**
     * Returns a complete list of all possible product variants.
     *
     * @return array
     */
    public function index(): array
    {
        return VariantsBuilder::context($this->context)
            ->build();
    }
}
