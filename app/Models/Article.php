<?php

namespace App\Models;

use App\Models\Traits\HasFilesOfType;
use App\Models\Traits\Sluggable;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Str;

class Article extends Model
{
    const DEFAULT_ARTICLE_SUMMARY_LENGTH = 200;

    use HasDateTimeFormatter;
    use HasFilesOfType;
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
        'setting' => 'json',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_category_pivots');
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
