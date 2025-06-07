<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class PageBlock extends Model
{
    protected $fillable = [
        'block_group_id',
        'title',
        'class',
        'remark',
        'description',
        'instruction',
        'parameters',
        'settings',
        'status',
    ];

    protected $casts = [
        'parameters' => 'array',
        'settings'   => 'array',
        'status'     => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(PageBlockGroup::class, 'block_group_id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(PageBlockSetting::class, 'block_id');
    }
}
