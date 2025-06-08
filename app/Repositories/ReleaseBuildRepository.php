<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ReleaseBuild;
use App\Repositories\Traits\Status;

final class ReleaseBuildRepository extends BaseRepository
{
    use Status;

    public function model(): string
    {
        return ReleaseBuild::class;
    }

    public function getLatestBuild(?string $platform, ?string $arch): ReleaseBuild
    {
        $query = $this->model->newQuery()
            ->status()
            ->orderByDesc('published_at');

        if (! empty($platform)) {
            $query->where('platform', $platform);
        }
        if (! empty($arch)) {
            $query->where('arch', $arch);
        }

        return $query->with(['version', 'assessable.file'])->firstOrFail();
    }
}
