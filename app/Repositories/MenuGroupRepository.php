<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\MenuGroup;

final class MenuGroupRepository extends BaseRepository
{
    public function model(): string
    {
        return MenuGroup::class;
    }

    public function getActiveGroups()
    {
        return $this->model->status()->orderBy('title')->get();
    }

    public function getActiveList(): array
    {
        return $this->model->status()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();
    }
}
