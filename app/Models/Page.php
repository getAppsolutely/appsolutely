<?php

namespace App\Models;

use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeReference;
use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use ScopePublished;
    use ScopeReference;
    use ScopeStatus;
    use Sluggable;

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

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlockSetting::class);
    }
}
