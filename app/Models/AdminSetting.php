<?php

namespace App\Models;

use Dcat\Admin\Models\Setting;

class AdminSetting extends Setting
{
    const PATH_PATTERNS = [
        'basic-logo'    => 'basic.logoPattern',
        'basic-favicon' => 'basic.faviconPattern',
    ];

    public function filesOfType(string $type)
    {
        return $this->morphToMany(File::class, 'assessable')
            ->wherePivot('type', $type)
            ->withTimestamps()
            ->withPivot('type'); // Optional: Include pivot data
    }
}
