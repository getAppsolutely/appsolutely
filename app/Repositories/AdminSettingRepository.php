<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AdminSetting;

final class AdminSettingRepository extends BaseRepository
{
    public function model(): string
    {
        return AdminSetting::class;
    }
}
