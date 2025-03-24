<?php

namespace App\Repositories;

use App\Models\AdminSetting;

class AdminSettingRepository
{
    public function find($id): ?AdminSetting
    {
        return AdminSetting::find($id);
    }
}
