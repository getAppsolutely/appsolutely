<?php

namespace App\Repositories;

use App\Models\PageContainerComponent;
use Illuminate\Database\Eloquent\Collection;

class PageContainerComponentRepository extends BaseRepository
{
    public function model(): string
    {
        return PageContainerComponent::class;
    }

    public function findByContainerId(int $containerId): Collection
    {
        return $this->model->newQuery()
            ->where('page_container_id', $containerId)
            ->orderBy('sort')
            ->get();
    }
}
