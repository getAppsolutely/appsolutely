<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PageBlock;

final class PageBlockRepository extends BaseRepository
{
    public function model(): string
    {
        return PageBlock::class;
    }
}
