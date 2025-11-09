<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ReleaseVersion extends Model
{
    use ScopePublished;
    use ScopeStatus;
    use SoftDeletes;

    protected $fillable = [
        'version',
        'remark',
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
        return $this->hasMany(ReleaseBuild::class, 'version_id');
    }
}
