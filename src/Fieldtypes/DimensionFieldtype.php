<?php

namespace Aerni\Snipcart\Fieldtypes;

use Aerni\Snipcart\Facades\Converter;
use Aerni\Snipcart\Facades\Dimension;
use Statamic\Facades\Site;
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
        return Dimension::from(Site::default())
            ->type($this->config('options'))
            ->all();
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param string|null $data
     * @return string|null
     */
    public function preProcess($data)
    {
        return Dimension::from(Site::default())
            ->type($this->config('options'))
            ->parse($data);
    }

    /**
     * Process the data before it gets saved.
     *
     * @param string|null $data
     * @return string|null
     */
    public function process($data)
    {
        return Dimension::from(Site::default())
            ->type($this->config('options'))
            ->parse($data);
    }

    /**
     * Process the data before it gets loaded into the view.
     *
     * @param int|null $data
     * @return string|null
     */
    public function augment($data)
    {
        return $this->convertUnit($this->config('options'), $data);
    }

    /**
     * Convert the entry unit to the site's unit.
     *
     * @param string $dimension
     * @param int|null $data
     * @return string
     */
    protected function convertUnit(string $dimension, $data): string
    {
        $entryUnit = $this->field()->parent()->get("{$dimension}_unit") ?? $this->field()->parent()->origin()->get("{$dimension}_unit");

        $siteUnit = Dimension::from(Site::current())
            ->type($dimension)
            ->short();

        $conversion = Converter::convert($data, $entryUnit, $siteUnit);

        return round($conversion, 2);
    }
}
