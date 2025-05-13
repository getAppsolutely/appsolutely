<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageContainerComponent extends Model
{
    protected $fillable = [
        'page_container_id',
        'component_name',
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

    public function container(): BelongsTo
    {
        return $this->belongsTo(PageContainer::class, 'page_container_id');
    }
}
