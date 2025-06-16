<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Status;
use App\Models\PageBlockSetting;
use DB;

final class PageBlockSettingRepository extends BaseRepository
{
    public function model(): string
    {
        return PageBlockSetting::class;
    }

    public function syncSetting(array $data, int $pageId): array
    {
        try {
            $result = [];
            DB::transaction(function () use ($data, &$result, $pageId) {
                foreach ($data as $index => $setting) {
                    $sort  = $index + 1;
                    $found = $this->model->newQuery()->where('page_id', $pageId)->where('block_id', $setting['id'])->first();
                    if ($found) {
                        $found->update(['status' => Status::ACTIVE, 'sort' => $sort]);

                        continue;
                    }

                    $data = [
                        'block_id' => $setting['id'],
                        'page_id'  => $pageId,
                        'status'   => Status::ACTIVE,
                        'sort'     => $sort,
                    ];
                    $result[] = PageBlockSetting::create($data);
                }
            });

            return $result;
        } catch (\Exception $exception) {
            log_error(__CLASS__ . '::' . __METHOD__ . '(): ' . $exception->getMessage());
        } finally {
            return $result;
        }

    }

    public function resetSetting(int $pageId): int
    {
        return $this->model->newQuery()
            ->where('page_id', $pageId)
            ->update(['status' => Status::INACTIVE, 'sort' => 0]);
    }
}
