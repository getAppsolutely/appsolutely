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
}
