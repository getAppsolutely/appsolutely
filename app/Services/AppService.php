<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AppBuild;
use App\Models\AppVersion;
use App\Repositories\AppBuildRepository;
use App\Repositories\AppVersionRepository;
use Illuminate\Support\Collection;

final class AppService
{
    public function __construct(
        protected AppVersionRepository $versionRepository,
        protected AppBuildRepository $buildRepository
    ) {}

    public function getVersions(): Collection
    {
        return $this->versionRepository->all();
    }

    public function getBuildsByVersion(int $versionId): Collection
    {
        return $this->buildRepository->findWhere(['version_id' => $versionId]);
    }

    public function createVersion(array $data): AppVersion
    {
        return $this->versionRepository->create($data);
    }

    public function createBuild(array $data): AppBuild
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

    public function getLatestBuild(string $platform, ?string $arch = null)
    {
        $query = $this->buildRepository->query()
            ->where('status', 1)
            ->where('platform', $platform)
            ->orderByDesc('published_at');

        if ($arch) {
            $query->where('arch', $arch);
        }

        return $query->with(['version', 'assessable.file'])->first();
    }
}
