<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ReleaseBuild;
use App\Models\ReleaseVersion;
use App\Repositories\ReleaseBuildRepository;
use App\Repositories\ReleaseVersionRepository;
use Illuminate\Support\Collection;

final class ReleaseService
{
    public function __construct(
        protected ReleaseVersionRepository $versionRepository,
        protected ReleaseBuildRepository $buildRepository
    ) {}

    public function getVersions(): Collection
    {
        return $this->versionRepository->all();
    }

    public function getBuildsByVersion(int $versionId): Collection
    {
        return $this->buildRepository->findWhere(['version_id' => $versionId]);
    }

    public function createVersion(array $data): ReleaseVersion
    {
        return $this->versionRepository->create($data);
    }

    public function createBuild(array $data): ReleaseBuild
    {
        return $this->buildRepository->create($data);
    }

    public function updateVersion(int $id, array $data): bool
    {
        return (bool) $this->versionRepository->update($data, $id);
    }

    public function updateBuild(int $id, array $data): bool
    {
        return (bool) $this->buildRepository->update($data, $id);
    }

    public function deleteVersion(int $id): bool
    {
        return (bool) $this->versionRepository->delete($id);
    }

    public function deleteBuild(int $id): bool
    {
        return (bool) $this->buildRepository->delete($id);
    }

    public function getLatestBuild(?string $platform = null, ?string $arch = null)
    {
        $query = $this->buildRepository->query()
            ->where('status', 1)
            ->orderByDesc('published_at');

        if ($platform) {
            $query->where('platform', $platform);
        }
        if ($arch) {
            $query->where('arch', $arch);
        }

        return $query->with(['version', 'assessable.file'])->first();
    }
}
