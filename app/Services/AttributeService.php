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
}
