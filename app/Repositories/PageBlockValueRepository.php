<?php

declare(strict_types=1);

namespace App\Repositories;

final class PageBlockValueRepository extends BaseRepository
{
    public function model(): string
    {
        return \App\Models\PageBlockValue::class;
    }

    /**
     * Find setting value by block ID
     */
    public function findByBlockId(int $blockId): ?\App\Models\PageBlockValue
    {
        return $this->model->newQuery()
            ->where('block_id', $blockId)
            ->first();
    }

    /**
     * Create or update setting value for a block
     */
    public function createOrUpdate(int $blockId, array $data): \App\Models\PageBlockValue
    {
        return $this->model->newQuery()
            ->updateOrCreate(
                ['block_id' => $blockId],
                $data
            );
    }

    /**
     * Delete setting value by block ID
     */
    public function deleteByBlockId(int $blockId): bool
    {
        return $this->model->newQuery()
            ->where('block_id', $blockId)
            ->delete();
    }
}
