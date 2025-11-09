<?php

declare(strict_types=1);

namespace App\DTOs\Examples;

use App\DTOs\ArticleCategoryData;
use App\DTOs\ArticleData;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Http\Request;

/**
 * Example usage of ArticleData
 *
 * This file demonstrates various ways to use ArticleData in your application.
 * DO NOT use this file directly - it's for reference only.
 */
class ArticleDataExample
{
    public function __construct(
        protected ArticleRepository $articleRepository
    ) {}

    /**
     * Example 1: Convert Article model to ArticleData
     */
    public function exampleFromModel(): ArticleData
    {
        $article = Article::with('categories')->find(1);

        // Convert model to Data object
        return ArticleData::fromModel($article);
    }

    /**
     * Example 2: Create ArticleData from array
     */
    public function exampleFromArray(): ArticleData
    {
        $data = [
            'title'        => 'My Article',
            'content'      => 'Article content here...',
            'status'       => 1,
            'published_at' => '2024-01-15 10:00:00',
        ];

        return ArticleData::from($data);
    }

    /**
     * Example 3: Create ArticleData from Request
     */
    public function exampleFromRequest(Request $request): ArticleData
    {
        // Automatic validation and type conversion
        return ArticleData::from($request->all());
    }

    /**
     * Example 4: Convert collection of Articles to DataCollection
     */
    public function exampleCollection(): \Spatie\LaravelData\DataCollection
    {
        $articles = Article::with('categories')
            ->where('status', 1)
            ->get();

        // Convert to DataCollection
        return ArticleData::collection($articles);
    }

    /**
     * Example 5: Use in API response
     */
    public function exampleApiResponse(int $id): array
    {
        $article = Article::with('categories')->find($id);

        if (! $article) {
            return ['error' => 'Article not found'];
        }

        $articleData = ArticleData::fromModel($article);

        // Return as array (automatically converts to JSON in response)
        return [
            'status' => true,
            'data'   => $articleData->toArray(),
        ];
    }

    /**
     * Example 6: Check if article is published
     */
    public function exampleCheckPublished(int $id): bool
    {
        $article     = Article::find($id);
        $articleData = ArticleData::fromModel($article);

        return $articleData->isPublished();
    }

    /**
     * Example 7: Get article URL
     */
    public function exampleGetUrl(int $id): ?string
    {
        $article     = Article::find($id);
        $articleData = ArticleData::fromModel($article);

        return $articleData->getUrl();
    }

    /**
     * Example 8: Create article from Data object
     */
    public function exampleCreateFromData(Request $request): Article
    {
        // Validate and create Data object
        $articleData = ArticleData::from($request->all());

        // Create article from Data object
        return Article::create($articleData->toArray());
    }

    /**
     * Example 9: Update article from Data object
     */
    public function exampleUpdateFromData(Article $article, Request $request): Article
    {
        // Validate and create Data object
        $articleData = ArticleData::from($request->all());

        // Update article
        $article->update($articleData->toArray());

        return $article->fresh();
    }

    /**
     * Example 10: Access nested category data
     */
    public function exampleNestedData(int $id): void
    {
        $article     = Article::with('categories')->find($id);
        $articleData = ArticleData::fromModel($article);

        // Access categories as DataCollection
        if ($articleData->categories) {
            foreach ($articleData->categories as $category) {
                // $category is ArticleCategoryData
                echo $category->title . "\n";
            }
        }
    }

    /**
     * Example 11: Filter published articles and convert to Data
     */
    public function exampleFilterPublished(): \Spatie\LaravelData\DataCollection
    {
        $articles = $this->articleRepository
            ->getPublishedArticles()
            ->with('categories')
            ->get();

        return ArticleData::collection($articles);
    }

    /**
     * Example 12: Convert to JSON for API
     */
    public function exampleToJson(int $id): string
    {
        $article     = Article::with('categories')->find($id);
        $articleData = ArticleData::fromModel($article);

        return $articleData->toJson();
    }
}
