<?php

namespace Aerni\Snipcart\Data;

use Illuminate\Support\Collection;
use Statamic\Entries\Entry;

class Variants
{
    public function __construct(protected Entry $entry)
    {
    }

    /**
     * Returns all possible product variants.
     */
    public function all(): ?Collection
    {
        if ($this->variations()->isEmpty()) {
            return null;
        }

        $variations = $this->variations();

        return collect($variations->shift())
            ->crossJoin(...$variations->map(fn ($variation) => $variation))
            ->map(fn ($variation) => $this->variant($variation));
    }

    /**
     * Prepares the variations data that is used to build the variants.
     */
    protected function variations(): Collection
    {
        return collect($this->entry->value('variations'))->map(function ($variation) {
            return collect($variation['options'])->map(fn ($option) => [
                'name' => $variation['name'],
                'option' => $option['name'],
                'price_modifier' => $option['price_modifier'],
            ]);
        });
    }

    /**
     * Returns a new variant object.
     */
    protected function variant(array $variation): array
    {
        return (new Variant($this->entry, $variation))->toArray();
    }
}
