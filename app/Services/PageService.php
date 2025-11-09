<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\BasicConstant;
use App\Enums\Status;
use App\Exceptions\TransactionException;
use App\Models\Page;
use App\Models\PageBlockSetting;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;
use App\Repositories\PageBlockValueRepository;
use App\Repositories\PageRepository;
use DB;
use Illuminate\Database\Eloquent\Model;

final class PageService
{
    public function __construct(
        protected PageRepository $pageRepository,
        protected PageBlockRepository $pageBlockRepository,
        protected PageBlockValueRepository $pageBlockValueRepository,
        protected PageBlockSettingRepository $pageBlockSettingRepository
    ) {}

    public function findPublishedPage(string $slug): ?Page
    {
        return $this->pageRepository->findPageBySlug($slug, now());
    }

    public function findPublishedPageById(int $id): ?Page
    {
        return $this->pageRepository->findPageById($id, now());
    }

    public function findByReference(string $reference): Model
    {
        return $this->pageRepository->with(['blocks'])->reference($reference)->firstOrFail();
    }

    public function resetSetting(string $reference): Model
    {
        $page = $this->findByReference($reference);
        $page->update(['setting' => []]);
        $this->pageBlockSettingRepository->resetSetting($page->id);

        return $page;
    }

    public function saveSetting(string $reference, array $data): Model
    {
        $page = $this->findByReference($reference);

        $blockData = \Arr::get($data, BasicConstant::PAGE_GRAPESJS_KEY);
        if (empty($blockData) || ! is_array($blockData)) {
            $blockData = [];
        }

        $this->pageBlockSettingRepository->resetSetting($page->id);
        $this->syncSettings($blockData, $page->id);

        $page->update(['setting' => $data]);

        return $page;
    }

    public function syncSettings(array $data, int $pageId): array
    {
        try {
            $result = [];
            DB::transaction(function () use ($data, &$result, $pageId) {
                foreach ($data as $index => $setting) {
                    $sort      = $index + 1;
                    $item      = $this->syncBlockSettingItem($setting, $sort, $pageId);
                    if (empty($item)) {
                        continue;
                    }
                    $result[] = $item;
                }
            });

            return $result;
        } catch (\Exception $exception) {
            log_error(
                'Failed to sync page block settings',
                [
                    'pageId' => $pageId,
                    'data'   => $data,
                    'error'  => $exception->getMessage(),
                ],
                __CLASS__,
                __METHOD__
            );
            throw new TransactionException(
                "Failed to sync page block settings for page ID {$pageId}: {$exception->getMessage()}",
                0,
                $exception
            );
        }
    }

    protected function syncBlockSettingItem(array $blockSetting, int $sort, int $pageId): array|PageBlockSetting
    {
        $blockId   = $blockSetting['block_id'];
        $reference = $blockSetting['reference'];
        if (empty($blockId) || empty($reference)) {
            log_warning('Invalid block id and reference', [
                'block_id'  => $blockId,
                'reference' => $reference,
            ]);

            return [];
        }

        $found = $this->pageBlockSettingRepository->findBy($pageId, $blockId, $reference);
        if ($found) {
            $found->update(['status' => Status::ACTIVE->value, 'sort' => $sort]);

            return [];
        }

        $data = [
            'page_id'        => $pageId,
            'block_id'       => $blockId,
            'block_value_id' => $this->getBlockValueId($blockId),
            'reference'      => $reference,
            'status'         => Status::ACTIVE->value,
            'sort'           => $sort,
            'published_at'   => now(),
        ];

        return $this->pageBlockSettingRepository->create($data);
    }

    public function getBlockValueId(int $blockId): int
    {
        // try to get value from the same block used in other blocks
        $setting = $this->pageBlockSettingRepository->findByBlockId($blockId);
        if (! empty($setting->block_value_id)) {
            return $setting->block_value_id;
        }

        // create if not used on all active pages
        $block = $this->pageBlockRepository->find($blockId);

        $value = [
            'block_id'      => $blockId,
            'schema_values' => $block->schema_values,
        ];
        $value = $this->pageBlockValueRepository->create($value);

        return $value->id;
    }
}
