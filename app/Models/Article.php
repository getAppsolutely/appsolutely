<?php

namespace App\Models;

use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\HasMarkdownContent;
use App\Models\Traits\ScopePublished;
use App\Models\Traits\ScopeStatus;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Str;

class Article extends Model
{
    const DEFAULT_ARTICLE_SUMMARY_LENGTH = 200;

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

    protected static function boot()
    {
        parent::boot();

        // Clear sitemap cache when article is saved or deleted
        static::saved(function () {
            app(\App\Services\SitemapService::class)->clearCache();
        });

        static::deleted(function () {
            app(\App\Services\SitemapService::class)->clearCache();
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_category_pivot');
    }

    public function contentSummary(): string
    {
        $length = config('misc.contentSummaryLength') ?? self::DEFAULT_ARTICLE_SUMMARY_LENGTH;

        return Str::limit($this->content, $length);
    }

    public function getContentFormattedAttribute(): ?string
    {
        $converter = new GithubFlavoredMarkdownConverter();
        try {
            return $converter->convert($this->content);
        } catch (CommonMarkException $e) {
            return null;
        }
    }
}
