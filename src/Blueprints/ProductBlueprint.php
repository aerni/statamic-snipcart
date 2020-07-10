<?php

namespace Aerni\Snipcart\Blueprints;

use Statamic\Facades\Blueprint;
use Statamic\Facades\YAML;
use Illuminate\Support\Str;

class ProductBlueprint
{
    public function __construct(string $title)
    {
        $blueprintYaml = file_get_contents(__DIR__ . '/../../resources/blueprints/product.yaml');
        $parsedYaml = YAML::parse($blueprintYaml);
        
        $parsedYaml['title'] = $title;

        Blueprint::make(Str::snake($title))->setContents($parsedYaml)->save();
    }
}