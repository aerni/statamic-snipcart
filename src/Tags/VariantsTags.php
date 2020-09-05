<?php

namespace Aerni\Snipcart\Tags;

use Statamic\Tags\Tags;
use Illuminate\Support\Collection;
use Aerni\Snipcart\Facades\Variant;
use Illuminate\Support\Facades\Request;

class VariantsTags extends Tags
{
    /**
     * The handle of the tag.
     *
     * @var string
     */
    protected static $handle = 'variants';

    /**
     * Returns all product variants.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return Variant::context($this->context)->all();
    }

    /**
     * Returns a specific product variant based on tag parameters or URL query.
     *
     * @return array
     */
    public function get(): array
    {
        if ($this->params->isEmpty()) {
            return [];
        }

        if ($this->params->bool('allow_query')) {
            $params = $this->params
                ->merge(Request::all())
                ->forget('allow_query');

            return Variant::context($this->context)->params($params)->get();
        }

        return Variant::context($this->context)->params($this->params)->get();
    }

    /**
     * Returns a complete list of all possible product variations.
     *
     * @return Collection
     */
    public function list(): Collection
    {
        return Variant::context($this->context)->list();
    }
}
