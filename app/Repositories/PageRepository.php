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

    public function findPublishedBySlug(array $slugs, Carbon $datetime): ?Page
    {
        return $this->model->newQuery()
            ->whereIn('slug', $slugs)
            ->status()
            ->published($datetime)
            ->with(['blocks' => function ($query) {
                $query->status()->whereNotNull('sort')->orderBy('sort');
            }, 'blocks.block'])

            ->first();
    }
}
