<?php

namespace App\Repositories;

use App\Models\Page;
use App\Repositories\Traits\Reference;
use App\Repositories\Traits\Status;
use Illuminate\Support\Carbon;

class PageRepository extends BaseRepository
{
    use Reference;
    use Status;

    public function model(): string
    {
        return Page::class;
    }

    public function findPageBySlug(string $slug, Carbon $datetime): ?Page
    {
        return $this->model->newQuery()
            ->slug($slug)
            ->status()
            ->published($datetime)
            ->with(['blocks' => function ($query) {
                $query->status()->whereNotNull('sort')->orderBy('sort');
            }, 'blocks.block'])
            ->first();
    }

    public function findPageById(int $id, Carbon $datetime): ?Page
    {
        return $this->model->newQuery()
            ->status()
            ->published($datetime)
            ->with(['blocks' => function ($query) {
                $query->status()->whereNotNull('sort')->orderBy('sort');
            }, 'blocks.block'])
            ->find($id);
    }
}
