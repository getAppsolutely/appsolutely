<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AppVersion;

final class AppVersionRepository extends BaseRepository
{
    public function model(): string
    {
        return AppVersion::class;
    }
}
