<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class AppVersion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'version',
        'remarks',
        'release_channel',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status'       => 'integer',
        'published_at' => 'datetime',
    ];

    public function builds(): HasMany
    {
        return $this->hasMany(AppBuild::class, 'version_id');
    }
}
