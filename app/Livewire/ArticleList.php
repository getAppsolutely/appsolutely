<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Repositories\ArticleRepository;
use Livewire\WithPagination;

final class ArticleList extends BaseBlock
{
    use WithPagination;

    protected function defaultConfig(): array
    {
        return [
            'title'               => 'Latest Articles',
            'subtitle'            => 'Stay updated with our latest articles and news',
            'show_featured_image' => true,
            'show_excerpt'        => true,
            'show_author'         => true,
            'show_date'           => true,
            'show_read_more'      => true,
            'read_more_text'      => 'Read More',
            'layout'              => 'grid', // grid, list, masonry
            'posts_per_page'      => 6,
            'category_filter'     => '', // empty for all categories
            'tag_filter'          => '', // empty for all tags
            'order_by'            => 'published_at', // published_at, title, created_at
            'order_direction'     => 'desc', // asc, desc
        ];
    }

    public function getArticlesProperty()
    {
        $filters = [
            'posts_per_page'  => $this->getData('posts_per_page', 6),
            'category_filter' => $this->getData('category_filter', ''),
            'tag_filter'      => $this->getData('tag_filter', ''),
            'order_by'        => $this->getData('order_by', 'published_at'),
            'order_direction' => $this->getData('order_direction', 'desc'),
        ];

        $query = app(ArticleRepository::class)->getPublishedArticles($filters);

        return $query->paginate($filters['posts_per_page']);
    }

    protected function getExtraData(): array
    {
        return [
            'articles' => $this->getArticlesProperty(),
        ];
    }
}
