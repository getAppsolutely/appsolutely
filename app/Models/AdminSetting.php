<?php

namespace App\Models;

use Dcat\Admin\Models\Setting;

class AdminSetting extends Setting
{
    public function filesOfType(string $type)
    {
        return $this->morphToMany(File::class, 'assessable')
            ->wherePivot('type', $type)
            ->withTimestamps()
            ->withPivot('type'); // Optional: Include pivot data
    }

}
