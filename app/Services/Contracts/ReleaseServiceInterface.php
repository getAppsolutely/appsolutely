<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\ReleaseBuild;

interface ReleaseServiceInterface
{
    /**
     * Get latest build for platform and architecture
     */
    public function getLatestBuild(?string $platform, ?string $arch): ReleaseBuild;
}
