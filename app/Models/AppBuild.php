<?php

namespace App\Models;

use App\Models\Traits\HasFilesOfType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class AppBuild extends Model
{
    use HasFilesOfType;
    use SoftDeletes;

    protected $fillable = [
        'version_id',
        'platform',
        'arch',
        'path',
        'force_update',
        'gray_strategy',
        'release_notes',
        'build_status',
        'build_log',
        'assessable_id',
        'signature',
        'status',
        'published_at',
    ];

    protected $casts = [
        'force_update'  => 'integer',
        'gray_strategy' => 'array',
        'status'        => 'integer',
        'published_at'  => 'datetime',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(AppVersion::class, 'version_id');
    }

    public function assessable(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Assessable::class, 'assessable_id');
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if (! empty($this->path)) {
            $path = $this->path;
        } elseif ($this->assessable && $this->assessable->file) {
            $path = $this->assessable->file->full_path;
        }
        if (empty($path)) {
            return null;
        }

        return public_url($path);
    }
}
