<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Page;
use App\Repositories\Traits\Reference;
use App\Repositories\Traits\Status;
use Illuminate\Support\Carbon;

final class PageRepository extends BaseRepository
{
    use Reference;
    use Status;

    public function model(): string
    {
        return Page::class;
    }

    public function findPageBySlug(string $slug, Carbon $datetime): ?Page
    {
        return $this->model->newQuery()
            ->slug($slug)
            ->status()
            ->published($datetime)
            ->with([
                'blocks' => function ($query) {
                    $query->status()->whereNotNull('sort')->orderBy('sort');
                },
                'blocks.block',
                'blocks.blockValue',
            ])
            ->first();
    }

    public function findPageById(int $id, Carbon $datetime): ?Page
    {
        return $this->model->newQuery()
            ->status()
            ->published($datetime)
            ->with([
                'blocks' => function ($query) {
                    $query->status()->whereNotNull('sort')->orderBy('sort');
                },
                'blocks.block',
                'blocks.blockValue',
            ])
            ->find($id);
    }

    /**
     * Get all published pages for sitemap generation
     */
    public function getPublishedPagesForSitemap(Carbon $datetime): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->newQuery()
            ->status()
            ->published($datetime)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * Update page setting
     */
    public function updateSetting(int $id, array $setting): Page
    {
        return $this->update(['setting' => $setting], $id);
    }

    /**
     * Find page by slug without datetime filtering (for admin use)
     */
    public function findBySlug(string $slug): ?Page
    {
        return $this->model->newQuery()
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get pages by parent ID
     */
    public function getByParentId(?int $parentId, ?Carbon $datetime = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->model->newQuery()
            ->where('parent_id', $parentId)
            ->status();

        if ($datetime !== null) {
            $query->published($datetime);
        }

        return $query->orderBy('published_at', 'desc')->get();
    }

    /**
     * Get published pages with blocks eager loaded
     */
    public function getPublishedWithBlocks(?Carbon $datetime = null): \Illuminate\Database\Eloquent\Collection
    {
        $datetime = $datetime ?? now();

        return $this->model->newQuery()
            ->status()
            ->published($datetime)
            ->with([
                'blocks' => function ($query) {
                    $query->status()->whereNotNull('sort')->orderBy('sort');
                },
                'blocks.block',
                'blocks.blockValue',
            ])
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
