<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Status;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessable extends Model
{
    use ScopePublished;
    use ScopeStatus;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'file_id',
        'assessable_type',
        'assessable_id',
        'type',
        'file_path',
        'title',
        'keyword',
        'description',
        'content',
        'config',
        'status',
        'published_at',
        'expired_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'config'       => 'array',
        'status'       => Status::class,
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
    ];

    /**
     * Get the file that owns the assessable.
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
