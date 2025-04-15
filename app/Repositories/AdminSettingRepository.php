<?php

namespace App\Repositories;

use App\Models\AdminSetting;

class AdminSettingRepository extends BaseRepository
{
    public function model(): string
    {
        return AdminSetting::class;
    }
}
