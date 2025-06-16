<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PageBlockSetting;
use DB;

final class PageBlockSettingRepository extends BaseRepository
{
    public function model(): string
    {
        return PageBlockSetting::class;
    }

    /**
     * @throws \Throwable
     */
    public function createInBatch(array $data, int $pageId): array
    {
        $result = [];
        DB::transaction(function () use ($data, &$result, $pageId) {
            foreach ($data as $setting) {
                $setting['block_id'] = $setting['id'];
                $setting['page_id']  = $pageId;
                unset($setting['id'], $setting['type']);
                $result[] = PageBlockSetting::create($setting);
            }
        });

        return $result;
    }

    public function disableInBatch(array $ids): void
    {
        $this->model->newQuery()->whereIn('id', $ids)->update(['status' => false]);
    }
}
