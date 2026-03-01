<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Status;
use App\Models\PageBlockSetting;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;
use App\Repositories\PageBlockValueRepository;
use App\Services\Contracts\PageBlockSettingServiceInterface;
use App\Services\Contracts\ThemeServiceInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\QueryException;
use PDOException;

/**
 * Service for managing page block settings synchronization
 *
 * This service handles the synchronization of block settings from the page builder
 * to the database. It manages:
 *
 * - Syncing block settings from GrapesJS data structure
 * - Creating/updating block settings with proper sort order
 * - Managing block values (reusing existing or creating new)
 *
 * Separated from PageService to follow Single Responsibility Principle.
 */
final readonly class PageBlockSettingService implements PageBlockSettingServiceInterface
{
    public function __construct(
        protected PageBlockRepository $pageBlockRepository,
        protected PageBlockValueRepository $pageBlockValueRepository,
        protected PageBlockSettingRepository $pageBlockSettingRepository,
        protected ThemeServiceInterface $themeService,
        protected ConnectionInterface $db
    ) {}

    public function syncSettings(array $data, int $pageId): array
    {
        try {
            $result = [];
            // Wrap all operations in a transaction to ensure data consistency
            // If any block setting fails, the entire operation is rolled back
            $this->db->transaction(function () use ($data, &$result, $pageId) {
                // Process each block setting in order (sort order is based on array index)
                foreach ($data as $index => $setting) {
                    $sort = $index + 1; // Sort order starts at 1, not 0
                    $item = $this->syncBlockSettingItem($setting, $sort, $pageId);
                    // Skip invalid or duplicate items (empty array returned)
                    if (empty($item)) {
                        continue;
                    }
                    $result[] = $item;
                }
            });

            return $result;
        } catch (QueryException|PDOException $exception) {
            log_error(
                'Failed to sync page block settings: database error',
                [
                    'pageId' => $pageId,
                    'data'   => $data,
                    'error'  => $exception->getMessage(),
                ],
                __CLASS__,
                __METHOD__
            );
            throw new \App\Exceptions\TransactionException(
                "Failed to sync page block settings for page ID {$pageId}: {$exception->getMessage()}",
                'Unable to save page settings. Please try again.',
                $exception,
                ['pageId' => $pageId]
            );
        } catch (\Exception $exception) {
            log_error(
                'Failed to sync page block settings: unexpected error',
                [
                    'pageId' => $pageId,
                    'data'   => $data,
                    'error'  => $exception->getMessage(),
                ],
                __CLASS__,
                __METHOD__
            );
            throw new \App\Exceptions\TransactionException(
                "Failed to sync page block settings for page ID {$pageId}: {$exception->getMessage()}",
                'Unable to save page settings. Please try again.',
                $exception,
                ['pageId' => $pageId]
            );
        }
    }

    /**
     * Sync a single block setting item
     */
    protected function syncBlockSettingItem(array $blockSetting, int $sort, int $pageId): array|PageBlockSetting
    {
        // Extract required identifiers from block setting data
        $blockId   = $blockSetting['block_id'];
        $reference = $blockSetting['reference'];

        // Validate required fields - both block_id and reference are mandatory
        if (empty($blockId) || empty($reference)) {
            log_warning('Invalid block id and reference', [
                'block_id'  => $blockId,
                'reference' => $reference,
            ]);

            return []; // Return empty array to skip this item
        }

        // Check if this block setting already exists for this page
        $found = $this->pageBlockSettingRepository->findBy($pageId, $blockId, $reference);
        if ($found) {
            // Update existing setting: reactivate it and update sort order
            // This handles cases where blocks are reordered or reactivated
            $this->pageBlockSettingRepository->updateStatusAndSort(
                $found->id,
                Status::ACTIVE->value,
                $sort
            );

            return []; // Return empty array since we updated, not created
        }

        // Create new block setting with all required data
        $theme = $this->themeService->resolveThemeName();
        $data  = [
            'page_id'        => $pageId,
            'block_id'       => $blockId,
            'block_value_id' => $this->getBlockValueId($blockId, $theme), // Get or create block value for current theme
            'reference'      => $reference,
            'status'         => Status::ACTIVE->value,
            'sort'           => $sort,
            'published_at'   => now(),
        ];

        return $this->pageBlockSettingRepository->create($data);
    }

    public function getBlockValueId(int $blockId, ?string $theme = null): int
    {
        // Try to reuse existing block value for this block and theme
        $existing = $this->pageBlockValueRepository->findByBlockIdAndTheme($blockId, $theme);
        if ($existing !== null) {
            return $existing->id;
        }

        // No existing block value found - create a new one for this theme
        $value = $this->pageBlockValueRepository->create([
            'block_id' => $blockId,
            'theme'    => $theme,
        ]);

        return $value->id;
    }
}
