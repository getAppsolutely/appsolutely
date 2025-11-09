<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArticleCategory;
use App\Repositories\Traits\ActiveTreeList;

final class ArticleCategoryRepository extends BaseRepository
{
    use ActiveTreeList;

    public function model(): string
    {
        return ArticleCategory::class;
    }
}
