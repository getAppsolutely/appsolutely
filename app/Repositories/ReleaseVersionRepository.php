<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ReleaseVersion;

final class ReleaseVersionRepository extends BaseRepository
{
    public function model(): string
    {
        return ReleaseVersion::class;
    }
}
