<?php

namespace App\Repositories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PageRepository extends BaseRepository
{
    public function model(): string
    {
        return Page::class;
    }

    public function findPublishedBySlug(string $slug): ?Page
    {
        $now = Carbon::now();

        return $this->model->newQuery()
            ->where('slug', $slug)
            ->where('status', 1)
            ->where('published_at', '<=', $now)
            ->where(function (Builder $query) use ($now) {
                $query->where('expired_at', '>', $now)
                    ->orWhereNull('expired_at');
            })
            /*
            ->with(['containers' => function ($query) use ($now) {
                $query->where('status', 1)
                    ->where('published_at', '<=', $now)
                    ->where(function ($q) use ($now) {
                        $q->where('expired_at', '>', $now)
                            ->orWhereNull('expired_at');
                    });
            }, 'containers.components' => function ($query) use ($now) {
                $query->where('status', 1)
                    ->where('published_at', '<=', $now)
                    ->where(function ($q) use ($now) {
                        $q->where('expired_at', '>', $now)
                            ->orWhereNull('expired_at');
                    });
            }])
            */
            ->first();
    }
}
