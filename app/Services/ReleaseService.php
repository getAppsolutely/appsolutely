<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ReleaseBuild;
use App\Repositories\ReleaseBuildRepository;
use App\Repositories\ReleaseVersionRepository;
use App\Services\Contracts\ReleaseServiceInterface;

final readonly class ReleaseService implements ReleaseServiceInterface
{
    public function __construct(
        protected ReleaseVersionRepository $versionRepository,
        protected ReleaseBuildRepository $buildRepository
    ) {}

    public function getLatestBuild(?string $platform, ?string $arch): ReleaseBuild
    {
        return $this->buildRepository->getLatestBuild($platform, $arch);
    }
}
