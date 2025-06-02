<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class AppBuild extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'version_id',
        'platform',
        'arch',
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
}
