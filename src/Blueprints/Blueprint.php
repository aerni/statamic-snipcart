<?php

namespace Aerni\Snipcart\Blueprints;

use Statamic\Facades\Blueprint as StatamicBlueprint;
use Statamic\Facades\YAML;

class Blueprint
{
    /**
     * The parsed blueprint content.
     *
     * @var array
     */
    protected $content;

    /**
     * The blueprint instance.
     *
     * @var StatamicBlueprint
     */
    protected $blueprint;

    /**
     * Get the blueprint Yaml as an array.
     *
     * @param string $path
     * @return array
     */
    public function parse(string $path): self
    {
        $blueprint = file_get_contents(__DIR__ . "/../../resources/blueprints/{$path}");
        $this->content = YAML::parse($blueprint);
        return $this;
    }

    /**
     * Make a blueprint.
     *
     * @param string $handle
     * @return self
     */
    public function make(string $handle): self
    {
        $this->blueprint = StatamicBlueprint::make($handle);
        return $this;
    }

    /**
     * Set the namespace on the blueprint.
     *
     * @param string $namespace
     * @return self
     */
    public function namespace(string $namespace): self
    {
        $this->blueprint->setNamespace($namespace);
        return $this;
    }

    /**
     * Set the contents on the blueprint and save it.
     *
     * @return void
     */
    public function save(): void
    {
        $this->blueprint->setContents($this->content)->save();
    }
}
