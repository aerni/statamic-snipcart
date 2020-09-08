<?php

namespace Aerni\Snipcart\Repositories;

use Statamic\Stache\Repositories\EntryRepository as StatamicEntryRepository;

class EntryRepository extends StatamicEntryRepository
{
    public function createRules($collection, $site)
    {
        $rules = parent::createRules($collection, $site);

        if ($this->isProduct($collection)) {
            $rules['sku'] = 'required|unique_entry_value:'.$collection->handle().',null,'.$site->handle();
        }

        return $rules;
    }

    public function updateRules($collection, $entry)
    {
        $rules = parent::updateRules($collection, $entry);

        if ($this->isProduct($collection)) {
            $rules['sku'] = 'required|unique_entry_value:'.$collection->handle().','.$entry->id().','.$entry->locale();
        }

        return $rules;
    }

    /**
     * Returns true when the given collection is a product
     *
     * @param \Statamic\Entries\Collection $collection
     * @return bool
     */
    protected function isProduct($collection): bool
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
