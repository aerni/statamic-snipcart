<?php

namespace Aerni\Snipcart;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Statamic\Entries\Entry;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Facades\Image;
use Statamic\Tags\Context;

class Product
{
    /**
     * The product instance.
     *
     * @var Entry
     */
    protected $product;

    /**
     * Construct the class.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->product = $this->product($context);
    }

    /**
     * Get the product instance.
     *
     * @param Context $context
     * @return Entry
     */
    protected function product(Context $context): Entry
    {
        return EntryFacade::find($context->get('id'));
    }

    /**
     * Get the Snipcart attributes from the product entry.
     *
     * @return Collection
     */
    public function attributes(): Collection
    {
        $data = $this->product->data();

        $data->put('url', Request::url());
        $data->put('id', $this->productId());

        return $this->transformAttributes($data);
    }

    /**
     * Get the product's ID.
     *
     * @return string
     */
    protected function productId(): string
    {
        return $this->product->data()->get('product_id') ?? $this->product->id();
    }

    /**
     * Transform the attributes to match the format that Snipcart expects.
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function transformAttributes(Collection $attributes): Collection
    {
        $transformedAttributes = $attributes->mapWithKeys(function ($item, $key) {
            if ($key === 'title') {
                return ['name' => $item];
            }

            if ($key === 'images' && is_array($item)) {
                return ['image' => $this->imagePath($item[0])];
            }

            if ($key === 'categories' && is_array($item)) {
                return [$key => implode('|', $item)];
            }

            if ($key === 'taxes' && is_array($item)) {
                return [$key => implode('|', $item)];
            }
            
            if ($key === 'custom_fields' && is_array($item)) {
                return $this->mapCustomFields($item);
            }

            if ($key === 'metadata' && is_array($item)) {
                return [$key => json_encode($item)];
            }

            return [$key => $item];
        });

        return $transformedAttributes;
    }

    /**
     * Transform the custom fields to fit the expected format.
     *
     * @param array $customFields
     * @return array
     */
    protected function mapCustomFields(array $customFields): Collection
    {
        $customFields = collect($customFields)->map(function ($item, $key) {
            $key++;

            if (! $item['enabled']) {
                return;
            }

            if ($item['type'] === 'dropdown') {
                return $this->mapDropdown($item, $key);
            }

            if ($item['type'] === 'checkbox') {
                return $this->mapCheckbox($item, $key);
            }

            if ($item['type'] === 'text' && $item['field_type'] === 'text') {
                return $this->mapTextField($item, $key);
            }

            if ($item['type'] === 'text' && $item['field_type'] === 'textarea') {
                return $this->mapTextarea($item, $key);
            }
        })->filter()->values();

        return $this->reassignFieldIndex($customFields);
    }

    /**
     * Make sure to start properly index custom fields starting with 1.
     *
     * @param Collection $customFields
     * @return Collection
     */
    protected function reassignFieldIndex(Collection $customFields): Collection
    {
        return $customFields->flatMap(function ($item, $index) {
            $index++;

            return collect($item)->mapWithKeys(function ($item, $key) use ($index) {
                $newKey = preg_replace("/[0-9]/", $index, $key);

                return [$newKey => $item];
            });
        });
    }

    /**
     * Map dropdowns to the proper structure.
     *
     * @param array $item
     * @param string $key
     * @return array
     */
    protected function mapDropdown(array $item, string $key): array
    {
        $options = collect($item['options'])->map(function ($item) {
            $name = $item['name'];
            $price = $item['price'];

            if (empty($price)) {
                return $name;
            }
            
            if (! Str::startsWith($price, ['+', '-'])) {
                $price = "+{$price}";
            }
            
            return "{$name}[{$price}]";
        })->implode('|');

        return [
            "custom{$key}-name" => $item['name'],
            "custom{$key}-options" => $options,
        ];
    }

    /**
     * Map checkboxes to the proper structure.
     *
     * @param array $item
     * @param string $key
     * @return array
     */
    protected function mapCheckbox(array $item, string $key): array
    {
        return [
            "custom{$key}-name" => $item['name'],
            "custom{$key}-type" => 'checkbox',
        ];
    }

    /**
     * Map text fields to the proper structure.
     *
     * @param array $item
     * @param string $key
     * @return array
     */
    protected function mapTextField(array $item, string $key): array
    {
        return [
            "custom{$key}-name" => $item['name'],
            "custom{$key}-value" => $item['default'],
            "custom{$key}-placeholder" => $item['placeholder'],
            "custom{$key}-required" => json_encode($item['required']),
        ];
    }

    /**
     * Map textarea fields to the proper structure.
     *
     * @param array $item
     * @param string $key
     * @return array
     */
    protected function mapTextarea(array $item, string $key): array
    {
        return [
            "custom{$key}-name" => $item['name'],
            "custom{$key}-type" => $item['field_type'],
            "custom{$key}-value" => $item['default'],
            "custom{$key}-placeholder" => $item['placeholder'],
            "custom{$key}-required" => json_encode($item['required']),
        ];
    }

    /**
     * Get the URL of an image.
     *
     * @param string $image
     * @return string
     */
    protected function imagePath(string $image): string
    {
        $imagePath = $this->assetContainerPath() . '/' . $image;

        if (config('snipcart.image.manipulation')) {
            return Image::manipulate($imagePath, config('snipcart.image.preset'));
        }

        return $imagePath;
    }

    /**
     * Get the path of the asset container.
     *
     * @return string
     */
    protected function assetContainerPath(): string
    {
        return Cache::remember('asset_container_path', 3600, function () {
            return AssetContainer::find($this->assetContainer())->url();
        });
    }

    /**
     * Get the asset container from the blueprint.
     *
     * @return string
     */
    protected function assetContainer(): string
    {
        $blueprintFields = $this->product->blueprint()->contents()['sections'];

        $imageField = collect($blueprintFields)->flatMap(function ($item) {
            return collect($item['fields'])->first(function ($item) {
                return $item['handle'] === 'images';
            });
        });

        return $imageField['field']['container'];
    }
}
