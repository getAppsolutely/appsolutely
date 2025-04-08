<?php

namespace App\Models;

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
    use SoftDeletes;

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_category_pivots',
            'article_id', 'article_category_id');
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
