<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\HasMarkdownContent;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFilesOfType;
    use HasMarkdownContent;
    use ScopePublished;
    use ScopeStatus;
    use Sluggable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'keywords',
        'description',
        'content',
        'slug',
        'cover',
        'status',
        'published_at',
        'expired_at',
        'sort',
    ];

    protected $casts = [
        'setting'      => 'array',
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
    ];

    // Boot method removed - sitemap cache clearing moved to ArticleObserver

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_category_pivot');
    }

    // Business logic methods moved to ArticleService for better separation of concerns
    // Use ArticleService::getContentSummary() and ArticleService::getFormattedContent()
}
