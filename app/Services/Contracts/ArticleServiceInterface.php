<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Article;

interface ArticleServiceInterface
{
    /**
     * Get content summary for an article
     */
    public function getContentSummary(Article $article): string;

    /**
     * Get formatted content (Markdown to HTML)
     */
    public function getFormattedContent(Article $article): ?string;
}
