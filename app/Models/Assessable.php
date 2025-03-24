<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Assessable extends Model
{
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
        'status',
        'created_at',
        'updated_at',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
