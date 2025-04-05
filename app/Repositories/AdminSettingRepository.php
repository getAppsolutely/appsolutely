<?php

namespace App\Repositories;

use App\Models\AdminSetting;

class AdminSettingRepository extends BaseRepository
{
    public function __construct(AdminSetting $model)
    {
        $this->model = $model;
    }
}
