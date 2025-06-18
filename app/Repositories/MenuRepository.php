<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Menu;

final class MenuRepository extends BaseRepository
{
    public function model(): string
    {
        return Menu::class;
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

    public function findByReference(string $reference): ?Menu
    {
        return $this->model->where('reference', $reference)->first();
    }

    public function getActiveListByReference(): array
    {
        return $this->model->status()
            ->orderBy('title')
            ->pluck('title', 'reference')
            ->toArray();
    }
}
