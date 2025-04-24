<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\AttributeValue;
use App\Repositories\AttributeGroupRepository;
use App\Repositories\AttributeRepository;
use App\Repositories\AttributeValueRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class AttributeService
{
    public function __construct(
        protected AttributeGroupRepository $attributeGroupRepository,
        protected AttributeRepository $attributeRepository,
        protected AttributeValueRepository $attributeValueRepository,
    ) {}

    /**
     * Create a new attribute group
     */
    public function createAttributeGroup(array $data): AttributeGroup
    {
        return $this->attributeGroupRepository->create($data);
    }

    /**
     * Update an attribute group
     */
    public function updateAttributeGroup(int $id, array $data): AttributeGroup
    {
        return $this->attributeGroupRepository->update($id, $data);
    }

    /**
     * Delete an attribute group
     */
    public function deleteAttributeGroup(int $id): bool
    {
        return $this->attributeGroupRepository->delete($id);
    }

    /**
     * Get all attribute groups
     */
    public function getAllAttributeGroups(): Collection
    {
        return $this->attributeGroupRepository->all();
    }

    /**
     * Create a new attribute
     */
    public function createAttribute(array $data): Attribute
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        return $this->attributeRepository->create($data);
    }

    /**
     * Update an attribute
     */
    public function updateAttribute(int $id, array $data): Attribute
    {
        if (isset($data['title']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $this->attributeRepository->update($id, $data);
    }

    /**
     * Delete an attribute
     */
    public function deleteAttribute(int $id): bool
    {
        return $this->attributeRepository->delete($id);
    }

    /**
     * Get all attributes
     */
    public function getAllAttributes(): Collection
    {
        return $this->attributeRepository->all();
    }

    /**
     * Create a new attribute value
     */
    public function createAttributeValue(array $data): AttributeValue
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['value']);

        return $this->attributeValueRepository->create($data);
    }

    /**
     * Update an attribute value
     */
    public function updateAttributeValue(int $id, array $data): AttributeValue
    {
        if (isset($data['value']) && ! isset($data['slug'])) {
            $data['slug'] = Str::slug($data['value']);
        }

        return $this->attributeValueRepository->update($id, $data);
    }

    /**
     * Delete an attribute value
     */
    public function deleteAttributeValue(int $id): bool
    {
        return $this->attributeValueRepository->delete($id);
    }

    /**
     * Get all attribute values
     */
    public function getAllAttributeValues(): Collection
    {
        return $this->attributeValueRepository->all();
    }

    /**
     * Get attribute values by attribute ID
     */
    public function getAttributeValuesByAttributeId(int $attributeId): Collection
    {
        return $this->attributeValueRepository->findWhere(['attribute_id' => $attributeId]);
    }

    /**
     * Sync attributes to an attribute group
     */
    public function syncAttributesToGroup(int $groupId, array $attributeIds): void
    {
        $group = $this->attributeGroupRepository->find($groupId);
        $group->attributes()->sync($attributeIds);
    }

    /**
     * Sync attribute values to a product SKU
     */
    public function syncAttributeValuesToProductSku(int $productSkuId, array $attributeValueIds): void
    {
        $productSku = app()->make('App\Repositories\ProductSkuRepository')->find($productSkuId);
        $productSku->attributeValues()->sync($attributeValueIds);
    }

    public static function attributeCacheKey(string $groupId, string $key): string
    {
        return sprintf('%s.attributes.%s_%s', appsolutely(), $groupId, $key);
    }

    public function findAttributesByGroupId($groupId): array
    {
        $data         = $this->attributeGroupRepository->with(['attributes.values'])->find($groupId);
        $attributes   =  $data?->attributes;
        $array        = self::collection2Array($attributes);
        $combinations = self::combinations($array);
        $result       = self::formattedCombination($combinations, $groupId);

        return $result;
    }

    protected function collection2Array($attributes): array
    {
        return $attributes->map(function ($attribute) {
            $item           = $attribute->toArray();
            $item['values'] = collect($item['values'])->map(function ($value) use ($attribute) {
                return array_merge($value, [
                    'attribute_title' => $attribute->title,
                    'attribute_slug'  => $attribute->slug,
                ]);
            })->all();

            return $item;
        })->all();
    }

    protected function combinations(array $array): array
    {
        $valueSets = array_map(fn ($attr) => $attr['values'], $array);

        return \Arr::crossJoin(...$valueSets);
    }

    protected function formattedCombination(array $combinations, string $groupId): array
    {
        return array_reduce($combinations, function ($item, $combo) use ($groupId) {
            $keys       = http_build_query(collect($combo)->sortBy('attribute_id')->pluck('id', 'attribute_id')->toArray());
            $titles     = http_build_query(collect($combo)->sortBy('attribute_title')->pluck('value', 'attribute_title')->toArray());
            $slugs      = http_build_query(collect($combo)->sortBy('attribute_slug')->pluck('slug', 'attribute_slug')->toArray());
            $key        = self::attributeCacheKey($groupId, $keys);
            $item[$key] =  [
                'key'      => $key,
                'keys'     => $keys,
                'title'    => $titles,
                'slug'     => $slugs,
                'readable' => str_replace(['&', '='], ['; ', ': '], $titles),
                'data'     => $combo,
            ];
            cache([$key => $item[$key]]);

            return $item;
        });
    }

    protected function flattenCombination(array $combinations, string $field): array
    {
        return array_map(fn ($item) => $item[$field], $combinations);
    }
}
