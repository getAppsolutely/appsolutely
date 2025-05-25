<?php

namespace App\Models;

use App\Models\Traits\LocalizesDateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use LocalizesDateTime;

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
