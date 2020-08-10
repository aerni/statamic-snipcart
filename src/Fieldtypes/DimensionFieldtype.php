<?php

namespace Aerni\Snipcart\Fieldtypes;

use Aerni\Snipcart\Facades\Dimension;
use Statamic\Fields\Fieldtype;

class DimensionFieldtype extends Fieldtype
{
    protected $icon = 'tags';

    protected function configFieldItems(): array
    {
        return [
            'options' => [
                'display' => __('snipcart::fieldtypes.dimension.display'),
                'instructions' => __('snipcart::fieldtypes.dimension.instructions'),
                'type' => 'select',
                'options' => [
                    'length' => __('snipcart::fieldtypes.dimension.options.length'),
                    'weight' => __('snipcart::fieldtypes.dimension.options.weight'),
                ],
                'default' => 'length',
                'width' => 50,
            ],
        ];
    }

    /**
     * Preload some data to be available in the vue component.
     *
     * @return array
     */
    public function preload(): array
    {
        return Dimension::type($this->config('options'))->all();
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function preProcess($data)
    {
        return Dimension::type($this->config('options'))->parse($data);
    }

    /**
     * Process the data before it gets saved.
     *
     * @param mixed $data
     * @return array|mixed
     */
    public function process($data)
    {
        return Dimension::type($this->config('options'))->parse($data);
    }
}
