<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\PageType;
use App\Repositories\ArticleRepository;
use Livewire\WithPagination;

final class ArticleList extends BaseBlock
{
    use WithPagination;

    protected array $defaultDisplayOptions = [
        'title'               => 'Latest Articles',
        'subtitle'            => 'Stay updated with our latest articles and news',
        'show_featured_image' => true,
        'show_excerpt'        => true,
        'show_author'         => true,
        'show_date'           => true,
        'show_read_more'      => true,
        'read_more_text'      => 'Read More',
        'layout'              => 'grid', // grid, list, masonry
    ];

    protected array $defaultQueryOptions = [
        'posts_per_page'  => 6,
        'category_filter' => '', // empty for all categories
        'tag_filter'      => '', // empty for all tags
        'order_by'        => 'published_at', // published_at, title, created_at
        'order_direction' => 'desc', // asc, desc
    ];

    protected function getExtraData(): array
    {
        return [
            'articles' => $this->loadArticles(),
        ];
    }

    protected function loadArticles()
    {
        if (isset($this->page['type']) && $this->page['type'] === PageType::Nested->value) {
            return collect();
        }

        $query = app(ArticleRepository::class)->getPublishedArticles($this->queryOptions);

        return $query->paginate($this->queryOptions['posts_per_page']);
    }
}
