<?php

namespace App\Services;

use App\Models\Asset;
use App\Repositories\AssetRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AssetService
{
    protected AssetRepository $assetRepository;

    public function __construct(AssetRepository $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function createAsset(array $data): Asset
    {
        return $this->assetRepository->create($data);
    }

    public function updateAsset(Asset $asset, array $data): bool
    {
        return $this->assetRepository->update($asset, $data);
    }

    public function deleteAsset(Asset $asset): bool
    {
        return $this->assetRepository->delete($asset);
    }

    public function findAsset(int $id): ?Asset
    {
        return $this->assetRepository->find($id);
    }

    public function findAssetByFilePath(string $filePath): ?Asset
    {
        return $this->assetRepository->findByFilePath($filePath);
    }

    /**
     * Get all assets for a model
     */
    public function getAssetsForModel(Model $model): Collection
    {
        return $this->assetRepository->findByAssetable($model);
    }

    /**
     * Get assets of a specific type for a model
     */
    public function getAssetsByType(Model $model, string $type): Collection
    {
        return $this->assetRepository->findByAssetableAndType($model, $type);
    }

    /**
     * Get a single asset of a specific type for a model
     */
    public function getAssetByType(Model $model, string $type): ?Asset
    {
        return $this->assetRepository->findOneByAssetableAndType($model, $type);
    }

    /**
     * Create an asset for a model
     */
    public function createAssetForModel(Model $model, array $data): Asset
    {
        $data['assetable_id'] = $model->id;
        $data['assetable_type'] = get_class($model);

        return $this->assetRepository->create($data);
    }

    /**
     * Create or update a single asset of a specific type for a model
     * Useful for cover images or other single-instance assets
     */
    public function setAssetForModel(Model $model, string $type, array $data): Asset
    {
        // Delete existing assets of this type
        $this->assetRepository->deleteByAssetableAndType($model, $type);

        // Set the type and model data
        $data['type'] = $type;
        $data['assetable_id'] = $model->id;
        $data['assetable_type'] = get_class($model);

        // Create the new asset
        return $this->assetRepository->create($data);
    }

    /**
     * Delete all assets for a model
     */
    public function deleteAssetsForModel(Model $model): int
    {
        return $this->assetRepository->deleteByAssetable($model);
    }

    /**
     * Delete assets of a specific type for a model
     */
    public function deleteAssetsByType(Model $model, string $type): int
    {
        return $this->assetRepository->deleteByAssetableAndType($model, $type);
    }
}
