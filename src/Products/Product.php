<?php

namespace Aerni\Snipcart\Products;

use Statamic\Facades\Entry;
use Illuminate\Support\Collection;
use Statamic\Contracts\Data\Augmented;
use Statamic\Data\HasAugmentedInstance;
use Statamic\Contracts\Data\Augmentable;
use Aerni\Snipcart\Products\Concerns\PreparesData;
use Aerni\Snipcart\Support\Validator;

class Product implements Augmentable
{
    use PreparesData, HasAugmentedInstance;

    public function __construct(string $id)
    {
        $this->entry = Entry::find($id);
        $this->data = $this->data();
    }

    public function toDataArray(): array
    {
        $data = collect([
            'name' => $this->name(),
            'id' => $this->id(),
            'price' => $this->price(),
            'image' => $this->image(),
            'description' => $this->description(),
            'file-guid' => $this->fileGuid(),
            'categories' => $this->categories(),
            'taxable' => $this->taxable(),
            'has-taxes-included' => $this->hasTaxesIncluded(),
            'taxes' => $this->taxes(),
            'shippable' => $this->shippable(),
            'stackable' => $this->stackable(),
            'metadata' => $this->metadata(),
            'length' => $this->length(),
            'width' => $this->width(),
            'height' => $this->height(),
            'weight' => $this->weight(),
            'quantity' => $this->quantity(),
            'quantity-step' => $this->quantityStep(),
            'min-quantity' => $this->minQuantity(),
            'max-quantity' => $this->maxQuantity(),
            'url' => $this->url(),
        ])->merge($this->customFields());

        return Validator::onlyValidAttributes($data)->all();
    }

    protected function data(): Collection
    {
        $localizedData = $this->entry->data()->only('price');

        return $this->entry->root()->data()
            ->merge($localizedData);
    }

    protected function rootVariants()
    {
        return $this->data->root()->get('variants');
    }

    public function newAugmentedInstance(): Augmented
    {
        return new AugmentedProduct($this);
    }
}
