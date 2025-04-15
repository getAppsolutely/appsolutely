<?php

namespace App\Repositories;

use App\Models\Article;

class ArticleRepository extends BaseRepository
{
    public function model(): string
    {
        return Article::class;
    }
}
