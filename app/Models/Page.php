<?php

declare(strict_types=1);

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
        'title',
        'name',
        'slug',
        'description',
        'keywords',
        'content',
        'setting',
        'canonical_url',
        'meta_robots',
        'og_title',
        'og_description',
        'og_image',
        'structured_data',
        'hreflang',
        'language',
        'parent_id',
        'published_at',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'expired_at'      => 'datetime',
        'status'          => 'integer',
        'setting'         => 'array',
        'structured_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->setting)) {
                $pageService   = app(\App\Services\Contracts\PageServiceInterface::class);
                $page->setting = $pageService->generateDefaultPageSetting();
            }
        });
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlockSetting::class);
    }
}
