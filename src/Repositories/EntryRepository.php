<?php

namespace Aerni\Snipcart\Repositories;

use Statamic\Entries\Collection;
use Statamic\Stache\Repositories\EntryRepository as Contract;

class EntryRepository extends Contract
{
    public function createRules($collection, $site)
    {
        $rules = parent::createRules($collection, $site);

        // TODO: Why do we need this?
        if ($this->isProduct($collection)) {
            $rules['sku'] = 'required|unique_entry_value:'.$collection->handle().',null,'.$site->handle();
        }

        return $rules;
    }

    public function updateRules($collection, $entry)
    {
        $rules = parent::updateRules($collection, $entry);

        // TODO: Why do we need this?
        if ($this->isProduct($collection)) {
            $rules['sku'] = 'required|unique_entry_value:'.$collection->handle().','.$entry->id().','.$entry->locale();
        }

        return $rules;
    }

    /**
     * Returns true when the given collection is a product.
     */
    protected function isProduct(Collection $collection): bool
    {
        if ($collection->handle() !== 'products') {
            return false;
        }

        if (! $collection->entryBlueprints()->has('product')) {
            return false;
        }

        return true;
    }
}
