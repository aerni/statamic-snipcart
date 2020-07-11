<?php

namespace Aerni\Snipcart\Blueprints;

use Statamic\Facades\Blueprint;
use Statamic\Facades\YAML;
use Illuminate\Support\Str;

class ProductBlueprint
{
    /**
     * Make the product blueprint with the given $title.
     *
     * @param string $title
     * @return void
     */
    public static function make(string $title): void
    {
        $blueprintYaml = file_get_contents(__DIR__ . '/../../resources/blueprints/product.yaml');
        $parsedYaml = YAML::parse($blueprintYaml);
        
        $parsedYaml['title'] = $title;

        Blueprint::make(Str::snake($title))->setContents($parsedYaml)->save();
    }
}