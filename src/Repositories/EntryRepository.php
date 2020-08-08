<?php

namespace Aerni\Snipcart\Repositories;

use Statamic\Stache\Repositories\EntryRepository as StatamicEntryRepository;

class EntryRepository extends StatamicEntryRepository
{
    public function createRules($collection, $site)
    {
        $rules = parent::createRules($collection, $site);

        if ($collection->handle() === 'products') {
            $rules['sku'] = 'required|unique_entry_value:'.$collection->handle().',null,'.$site->handle();
        }

        return $rules;
    }

    public function updateRules($collection, $entry)
    {
        $rules = parent::updateRules($collection, $entry);

        if ($collection->handle() === 'products') {
            $rules['sku'] = 'required|unique_entry_value:'.$collection->handle().','.$entry->id().','.$entry->locale();
        }

        return $rules;
    }
}
