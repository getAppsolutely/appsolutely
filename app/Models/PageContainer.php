<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageContainer extends Model
{
    protected $fillable = [
        'page_id',
        'html',
        'layout',
        'style',
        'config',
        'sort',
        'published_at',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'config'       => 'json',
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
        'status'       => 'integer',
        'sort'         => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(PageContainerComponent::class)->orderBy('sort');
    }
}
