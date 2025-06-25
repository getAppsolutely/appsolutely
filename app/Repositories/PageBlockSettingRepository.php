<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Status;
use App\Models\PageBlockSetting;

final class PageBlockSettingRepository extends BaseRepository
{
    public function model(): string
    {
        return PageBlockSetting::class;
    }

    public function findBy(?int $pageId, ?int $blockId, ?string $reference): ?PageBlockSetting
    {
        $query = $this->model->newQuery();
        if (! empty($pageId)) {
            $query->where('page_id', $pageId);
        }

        if (! empty($blockId)) {
            $query->where('block_id', $blockId);
        }

        if (! empty($reference)) {
            $query->where('reference', $reference);
        }

        return $query->first();
    }

    public function findByBlockId(int $blockId): ?PageBlockSetting
    {
        return $this->model->newQuery()->where('block_id', $blockId)->status()->first();
    }

    public function resetSetting(int $pageId): int
    {
        return $this->model->newQuery()
            ->where('page_id', $pageId)
            ->update(['status' => Status::INACTIVE, 'sort' => 0]);
    }

    public function getActivePublishedSettings(int $pageId, ?\Carbon\Carbon $datetime = null): \Illuminate\Database\Eloquent\Collection
    {
        $datetime = $datetime ?? now();

        return $this->model->newQuery()
            ->where('page_id', $pageId)
            ->status()
            ->published($datetime)
            ->orderBy('sort')
            ->get();
    }

    public function updatePublishStatus(int $id, ?string $publishedAt = null, ?string $expiredAt = null): int
    {
        $data = [];

        if ($publishedAt !== null) {
            $data['published_at'] = $publishedAt;
        }

        if ($expiredAt !== null) {
            $data['expired_at'] = $expiredAt;
        }

        return $this->model->newQuery()
            ->where('id', $id)
            ->update($data);
    }
}
