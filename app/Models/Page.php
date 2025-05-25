<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'published_at',
        'expired_at',
        'status',
        'title',
        'description',
        'keywords',
        'content',
        'canonical_url',
        'meta_robots',
        'og_title',
        'og_description',
        'og_image',
        'structured_data',
        'hreflang',
        'language',
        'parent_id',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'expired_at'      => 'datetime',
        'status'          => 'integer',
        'structured_data' => 'array',
    ];

    public function containers(): HasMany
    {
        return $this->hasMany(PageContainer::class)->orderBy('sort');
    }
}
