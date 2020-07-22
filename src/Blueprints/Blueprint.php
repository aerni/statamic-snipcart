<?php

namespace Aerni\Snipcart\Blueprints;

use Illuminate\Support\Str;
use Statamic\Facades\Blueprint as StatamicBlueprint;
use Statamic\Facades\YAML;

abstract class Blueprint
{
    /**
     * The parsed blueprint.
     *
     * @var array
     */
    protected $blueprint = [];

    /**
     * Construct the class with the given $filename.
     *
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->blueprint = $this->parseBlueprintYaml($filename);
    }

    /**
     * Make a blueprint with the given $title.
     *
     * @param string $title
     * @param string $namespace
     * @return void
     */
    public function make(string $title, string $namespace): void
    {
        $this->title($title);
        StatamicBlueprint::make(Str::snake($title))->setNamespace($namespace)->setContents($this->blueprint)->save();
    }

    /**
     * Parse the blueprint Yaml.
     *
     * @param string $filename
     * @return array
     */
    protected function parseBlueprintYaml(string $filename): array
    {
        $blueprintYaml = file_get_contents(__DIR__ . "/../../resources/blueprints/{$filename}.yaml");

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
