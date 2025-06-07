<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ReleaseBuild;

final class ReleaseBuildRepository extends BaseRepository
{
    public function model(): string
    {
        return ReleaseBuild::class;
    }
}
