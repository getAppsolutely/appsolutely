<?php

namespace App\Repositories;

use App\Models\PageContainer;
use Illuminate\Database\Eloquent\Collection;

class PageContainerRepository extends BaseRepository
{
    public function model(): string
    {
        return PageContainer::class;
    }

    public function findByPageId(int $pageId): Collection
    {
        return $this->model->newQuery()
            ->where('page_id', $pageId)
            ->orderBy('sort')
            ->get();
    }
}
