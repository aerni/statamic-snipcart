<?php

namespace Aerni\Snipcart\Blueprints;

use Illuminate\Support\Str;
use Statamic\Facades\Blueprint as BlueprintFacade;
use Statamic\Facades\YAML;

class Blueprint
{
    /**
     * The handle of the blueprint to copy and parse.
     *
     * @var string
     */
    protected $handle = '';

    /**
     * The parsed blueprint.
     *
     * @var array
     */
    protected $blueprint = [];

    /**
     * Construct the class with the given $handle.
     *
     * @param string $handle
     */
    public function __construct(string $handle)
    {
        $this->blueprint = $this->parseBlueprintYaml($handle);
    }

    /**
     * Make a blueprint with the given $title.
     *
     * @param string $title
     * @return void
     */
    public function make(string $title): void
    {
        $this->title($title);
        BlueprintFacade::make(Str::snake($title))->setContents($this->blueprint)->save();
    }

    /**
     * Parse the blueprint Yaml.
     *
     * @param string $handle
     * @return array
     */
    protected function parseBlueprintYaml(string $handle): array
    {
        $blueprintYaml = file_get_contents(__DIR__ . "/../../resources/blueprints/{$handle}.yaml");

        return YAML::parse($blueprintYaml);
    }

    /**
     * Set the title of the blueprint.
     *
     * @param string $title
     * @return self
     */
    protected function title(string $title): self
    {
        $this->blueprint['title'] = $title;

        return $this;
    }
}
