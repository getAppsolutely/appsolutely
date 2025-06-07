<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PageBlockSetting;

final class PageBlockSettingRepository extends BaseRepository
{
    public function model(): string
    {
        return PageBlockSetting::class;
    }
}
