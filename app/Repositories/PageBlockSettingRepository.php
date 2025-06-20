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
                    $sort      = $index + 1;
                    $blockId   = $setting['block_id'];
                    $reference = $setting['reference'];
                    if (empty($blockId) || empty($reference)) {
                        log_warning('Invalid block id and reference', [
                            'block_id'  => $blockId,
                            'reference' => $reference,
                        ]);

                        continue;
                    }
                    $found = $this->model->newQuery()
                        ->where('page_id', $pageId)
                        ->where('block_id', $blockId)
                        ->where('reference', $reference)
                        ->first();
                    if ($found) {
                        $found->update(['status' => Status::ACTIVE->value, 'sort' => $sort]);

                        continue;
                    }

                    $data = [
                        'block_id'     => $blockId,
                        'page_id'      => $pageId,
                        'reference'    => $reference,
                        'status'       => Status::ACTIVE->value,
                        'sort'         => $sort,
                        'published_at' => now(),
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

    public function updatePublishStatus(int $id, ?string $publishedAt = null, ?string $expiredAt = null): bool
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
