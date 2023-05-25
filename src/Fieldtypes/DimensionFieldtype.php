<?php

namespace Aerni\Snipcart\Fieldtypes;

use Statamic\Facades\Site;
use Statamic\Fields\Fieldtype;
use Aerni\Snipcart\Facades\Converter;
use Aerni\Snipcart\Facades\Dimension;
use Statamic\Sites\Site as StatamicSite;

class DimensionFieldtype extends Fieldtype
{
    protected function configFieldItems(): array
    {
        return [
            [
                'display' => __('Settings'),
                'fields' => [
                    'dimension' => [
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
                ],
            ],
        ];
    }

    /**
     * Preload some data to be available in the vue component.
     */
    public function preload(): array
    {
        return Dimension::from($this->rootSite())
            ->type($this->config('dimension'))
            ->all();
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     */
    public function preProcess(mixed $data): ?string
    {
        return Dimension::from($this->rootSite())
            ->type($this->config('dimension'))
            ->parse($data);
    }

    /**
     * Process the data before it gets saved.
     */
    public function process(mixed $data): ?string
    {
        return Dimension::from($this->rootSite())
            ->type($this->config('dimension'))
            ->parse($data);
    }

    /**
     * Process the data before it gets loaded into the view.
     */
    public function augment(mixed $data): ?string
    {
        return $this->convertUnit($this->config('dimension'), $data);
    }

    /**
     * Convert the entry unit to the site's unit.
     */
    protected function convertUnit(string $dimension, ?int $data): string
    {
        $rootUnit = Dimension::from($this->rootSite())
            ->type($dimension)
            ->short();

        $siteUnit = Dimension::from(Site::current())
            ->type($dimension)
            ->short();

        $conversion = Converter::convert($data, $rootUnit, $siteUnit);
        $rounded = round($conversion, 2);

        return "{$rounded} {$siteUnit}";
    }

    /**
     * Get the entry's root site.
     */
    protected function rootSite(): StatamicSite
    {
        return $this->field()->parent()->root()->site();
    }
}
