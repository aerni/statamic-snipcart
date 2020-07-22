<?php

namespace Aerni\Snipcart\Blueprints;

use Statamic\Facades\Blueprint as StatamicBlueprint;
use Statamic\Facades\YAML;

abstract class Blueprint
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
     * Construct the class.
     *
     * @param string $path
     * @param string $handle
     */
    public function __construct(string $path, string $handle)
    {
        $this->content = $this->parse($path);
        $this->blueprint = StatamicBlueprint::make($handle);
    }

    /**
     * Get the blueprint Yaml as an array.
     *
     * @param string $path
     * @return array
     */
    public function parse(string $path): array
    {
        $blueprint = file_get_contents(__DIR__ . "/../../resources/blueprints/{$path}");
        return YAML::parse($blueprint);
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
