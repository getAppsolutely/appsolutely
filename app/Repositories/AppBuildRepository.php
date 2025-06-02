<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AppBuild;

final class AppBuildRepository extends BaseRepository
{
    public function model(): string
    {
        return AppBuild::class;
    }
}
