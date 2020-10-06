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

    public function index(): array
    {
        return $this->context->value('variants');
    }

    /**
     * Returns a complete list of all possible product variants.
     *
     * @return array
     */
    public function all(): array
    {
        return VariantsBuilder::context($this->context)
            ->all();
    }
}
