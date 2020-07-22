<?php

namespace Aerni\Snipcart\Blueprints;

use Illuminate\Support\Str;
use Statamic\Facades\Blueprint as StatamicBlueprint;

class CategoryBlueprint extends Blueprint
{
    public function __construct()
    {
        parent::__construct('category');
    }

    /**
     * Make a blueprint with the given $title.
     *
     * @param string $title
     * @param string $handle
     * @return void
     */
    public function make(string $title, string $handle): void
    {
        $this->title($title);
        StatamicBlueprint::make(Str::snake($title))->setNamespace("taxonomies.{$handle}")->setContents($this->blueprint)->save();
    }
}
