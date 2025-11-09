<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\Contracts\ArticleServiceInterface;
use Illuminate\Support\Str;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * Service for article-related business logic
 *
 * Handles article formatting, content processing, and other business operations.
 * Separates business logic from the Article model.
 */
final readonly class ArticleService implements ArticleServiceInterface
{
    private const DEFAULT_ARTICLE_SUMMARY_LENGTH = 200;

    public function __construct(
        protected ArticleRepository $articleRepository
    ) {}

    /**
     * Get content summary for an article
     * Moved from Article model to service
     */
    public function getContentSummary(Article $article): string
    {
        $length = config('misc.contentSummaryLength') ?? self::DEFAULT_ARTICLE_SUMMARY_LENGTH;

        return Str::limit($article->content, $length);
    }

    /**
     * Get formatted content (Markdown to HTML)
     * Moved from Article model to service
     */
    public function getFormattedContent(Article $article): ?string
    {
        $converter = new GithubFlavoredMarkdownConverter();
        try {
            return $converter->convert($article->content);
        } catch (CommonMarkException $e) {
            return null;
        }
    }
}
