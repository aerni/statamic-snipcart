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
     * Returns an array of all product variants.
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return Variant::from($this->context->get('id'))->all();
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
            $params = $this->params->merge(Request::all())->forget('allow_query');
            return Variant::from($this->context->get('id'))->combine($params);
        }

        return Variant::from($this->context->get('id'))->combine($this->params);
    }

    /**
     * Get a list of all possible product variations.
     *
     * @return Collection
     */
    public function list(): Collection
    {
        return Variant::from($this->context->get('id'))->combinations();
    }
}
