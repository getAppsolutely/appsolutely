<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\BlockScope;
use App\Models\PageBlock;
use Illuminate\Database\Eloquent\Collection;

final class PageBlockRepository extends BaseRepository
{
    public function model(): string
    {
        return PageBlock::class;
    }

    /**
     * Get all global scope blocks that pages are using
     */
    public function getGlobalBlocks(): Collection
    {
        return $this->model->newQuery()
            ->with(['settings'])
            ->whereHas('settings', function ($query) {
                $query->status();
            })
            ->where('scope', BlockScope::Global->value)
            ->status()
            ->orderBy('id')
            ->get();
    }
}
