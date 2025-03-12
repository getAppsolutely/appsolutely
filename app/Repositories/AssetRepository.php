<?php

namespace App\Repositories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AssetRepository
{
    public function create(array $data): Asset
    {
        return Asset::create($data);
    }

    public function update(Asset $asset, array $data): bool
    {
        return $asset->update($data);
    }

    public function delete(Asset $asset): bool
    {
        return $asset->delete();
    }

    public function find(int $id): ?Asset
    {
        return Asset::find($id);
    }

    public function findByFilePath(string $filePath): ?Asset
    {
        return Asset::where('file_path', $filePath)->first();
    }

    /**
     * Find all assets for a specific model
     */
    public function findByAssetable(Model $assetable): Collection
    {
        return Asset::where('assetable_type', get_class($assetable))
                    ->where('assetable_id', $assetable->id)
                    ->get();
    }

    /**
     * Find assets of a specific type for a model
     */
    public function findByAssetableAndType(Model $assetable, string $type): Collection
    {
        return Asset::where('assetable_type', get_class($assetable))
                    ->where('assetable_id', $assetable->id)
                    ->where('type', $type)
                    ->get();
    }

    /**
     * Find a single asset of a specific type for a model
     */
    public function findOneByAssetableAndType(Model $assetable, string $type): ?Asset
    {
        return Asset::where('assetable_type', get_class($assetable))
                    ->where('assetable_id', $assetable->id)
                    ->where('type', $type)
                    ->first();
    }

    /**
     * Delete all assets for a specific model
     */
    public function deleteByAssetable(Model $assetable): int
    {
        return Asset::where('assetable_type', get_class($assetable))
                    ->where('assetable_id', $assetable->id)
                    ->delete();
    }

    /**
     * Delete assets of a specific type for a model
     */
    public function deleteByAssetableAndType(Model $assetable, string $type): int
    {
        return Asset::where('assetable_type', get_class($assetable))
                    ->where('assetable_id', $assetable->id)
                    ->where('type', $type)
                    ->delete();
    }
}
