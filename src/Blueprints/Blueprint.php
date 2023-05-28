<?php

namespace Aerni\Snipcart\Blueprints;

use Statamic\Facades\YAML;
use Statamic\Fields\Blueprint as StatamicBlueprint;
use Statamic\Facades\Blueprint as BlueprintApi;

class Blueprint
{
    /**
     * The parsed blueprint content.
     */
    protected array $content;

    /**
     * The blueprint instance.
     */
    protected StatamicBlueprint $blueprint;

    /**
     * Get the blueprint Yaml as an array.
     */
    public function parse(string $path): self
    {
        $blueprint = file_get_contents(__DIR__."/../../resources/blueprints/{$path}");
        $this->content = YAML::parse($blueprint);

        return $this;
    }

    /**
     * Make a blueprint.
     */
    public function make(string $handle): self
    {
        $this->blueprint = BlueprintApi::make($handle);

        return $this;
    }

    /**
     * Set the namespace on the blueprint.
     */
    public function namespace(string $namespace): self
    {
        $this->blueprint->setNamespace($namespace);

        return $this;
    }

    /**
     * Set the contents on the blueprint and save it.
     */
    public function save(): void
    {
        $this->blueprint->setContents($this->content)->save();
    }
}
