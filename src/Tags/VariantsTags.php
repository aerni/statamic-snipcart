<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\VariantsBuilder;
use Illuminate\Support\Facades\Request;
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
     * Returns a specific product variant.
     *
     * @return array
     */
    public function get(): array
    {
        if ($this->params->isEmpty()) {
            return $this->context->value('variants');
        }

        if ($this->params->bool('allow_query')) {
            $this->params = $this->params
                ->merge(Request::all())
                ->forget('allow_query');
        }

        return VariantsBuilder::context($this->context)
            ->params($this->params)
            ->get();
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
