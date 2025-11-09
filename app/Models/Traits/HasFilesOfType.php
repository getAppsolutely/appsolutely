<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\File;

trait HasFilesOfType
{
    /**
     * Get files of a specific type for this model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function filesOfType(string $type)
    {
        return $this->morphToMany(File::class, 'assessable')
            ->wherePivot('type', $type)
            ->withTimestamps()
            ->withPivot('type');
    }
}
