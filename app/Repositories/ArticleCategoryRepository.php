<?php

namespace App\Repositories;

use App\Models\ArticleCategory;
use App\Repositories\Traits\ActiveTreeList;

class ArticleCategoryRepository extends BaseRepository
{
    use ActiveTreeList;

    public function model(): string
    {
        return ArticleCategory::class;
    }
}
