<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PageBlockGroup;

final class PageBlockGroupRepository extends BaseRepository
{
    public function model(): string
    {
        return PageBlockGroup::class;
    }

    public function getCategorisedBlocks()
    {
        $query = $this->model->newQuery();

        return $query
            ->whereHas('blocks', function ($query) {
                $query->status();
            })->with(['blocks' => function ($query) {
                $query->orderBy('sort')->status();
            }])->status()->orderBy('sort')->get();
    }
}
