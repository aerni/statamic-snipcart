<?php

namespace Aerni\Snipcart\Tags;

use Aerni\Snipcart\Facades\Variations;
use Illuminate\Support\Facades\Request;
use Statamic\Tags\Tags;

class VariationsTags extends Tags
{
    /**
     * The handle of the tag.
     *
     * @var string
     */
    protected static $handle = 'variations';

    public function index(): array
    {
        return $this->context->value('variations');
    }

    /**
     * Returns a specific product variation.
     *
     * @return array
     */
    public function get(): array
    {
        if ($this->params->isEmpty()) {
            return $this->context->value('variations');
        }

        if ($this->params->bool('allow_query')) {
            $this->params = $this->params
                ->merge(Request::all())
                ->forget('allow_query');
        }

        return Variations::context($this->context)->get($this->params);
    }

    /**
     * Returns a complete list of all possible product variations.
     *
     * @return array
     */
    public function all(): array
    {
        return Variations::context($this->context)->all();
    }
}
