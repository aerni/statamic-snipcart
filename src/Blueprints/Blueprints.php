<?php

namespace Aerni\Snipcart\Blueprints;

use Statamic\Facades\Blueprint;
use Statamic\Facades\YAML;
use Illuminate\Support\Str;

class Blueprints
{
    /**
     * The handle of the blueprint to parse.
     *
     * @var string
     */
    protected $handle = '';

    /**
     * Make the blueprints with the given $title.
     *
     * @param string $title
     * @return void
     */
    public function make(string $title): void
    {
        $blueprintYaml = file_get_contents(__DIR__ . "/../../resources/blueprints/{$this->handle}.yaml");
        $parsedYaml = YAML::parse($blueprintYaml);
        
        $parsedYaml['title'] = $title;

        Blueprint::make(Str::snake($title))->setContents($parsedYaml)->save();
    }
}