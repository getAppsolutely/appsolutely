<?php

namespace App\Repositories;

use App\Models\ArticleCategory;
use App\Repositories\Traits\AvailableTreeList;

class ArticleCategoryRepository extends BaseRepository
{
    use AvailableTreeList;

    public function __construct(ArticleCategory $model)
    {
        $this->model = $model;
    }
}
