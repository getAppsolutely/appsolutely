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
use App\Services\Contracts\PageServiceInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PDOException;

final readonly class PageService implements PageServiceInterface
{
    public function __construct(
        protected PageRepository $pageRepository,
        protected PageBlockRepository $pageBlockRepository,
        protected PageBlockValueRepository $pageBlockValueRepository,
        protected PageBlockSettingRepository $pageBlockSettingRepository,
        protected ConnectionInterface $db
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
        $this->pageRepository->updateSetting($page->id, []);
        $this->pageBlockSettingRepository->resetSetting($page->id);

        return $this->pageRepository->find($page->id);
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

        $this->pageRepository->updateSetting($page->id, $data);

        return $this->pageRepository->find($page->id);
    }

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
            throw new TransactionException(
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
            throw new TransactionException(
                "Failed to sync page block settings for page ID {$pageId}: {$exception->getMessage()}",
                'Unable to save page settings. Please try again.',
                $exception,
                ['pageId' => $pageId]
            );
        }
    }

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
        $data = [
            'page_id'        => $pageId,
            'block_id'       => $blockId,
            'block_value_id' => $this->getBlockValueId($blockId), // Get or create block value
            'reference'      => $reference,
            'status'         => Status::ACTIVE->value,
            'sort'           => $sort,
            'published_at'   => now(),
        ];

        return $this->pageBlockSettingRepository->create($data);
    }

    public function getBlockValueId(int $blockId): int
    {
        // Try to reuse existing block value if this block is already used elsewhere
        // This prevents duplicate block values for the same block type
        $setting = $this->pageBlockSettingRepository->findByBlockId($blockId);
        if (! empty($setting->block_value_id)) {
            // Reuse existing block value ID to maintain data consistency
            return $setting->block_value_id;
        }

        // No existing block value found - create a new one
        // This happens when a block is used for the first time
        $block = $this->pageBlockRepository->find($blockId);

        // Create new block value with schema values from the block definition
        $value = [
            'block_id'      => $blockId,
            'schema_values' => $block->schema_values, // Copy schema structure from block
        ];
        $value = $this->pageBlockValueRepository->create($value);

        return $value->id;
    }

    /**
     * Generate default page setting structure
     * Moved from Page model to service for better separation of concerns
     */
    public function generateDefaultPageSetting(): array
    {
        $structure  = $this->getPageStructure();
        $components = $this->attachGlobalBlocks();
        Arr::set($structure, BasicConstant::PAGE_GRAPESJS_KEY, $components);

        return $structure;
    }

    /**
     * Get the basic page builder structure without components
     * Moved from Page model to service
     *
     * This creates the initial GrapesJS structure with:
     * - A single page with main type
     * - One frame containing the root wrapper component
     * - Empty arrays for assets, styles, symbols, and data sources
     */
    protected function getPageStructure(): array
    {
        return [
            'pages' => [
                [
                    'id'     => Str::random(16), // Unique page identifier
                    'type'   => 'main', // Main page type (GrapesJS convention)
                    'frames' => [
                        [
                            'id'        => Str::random(16), // Unique frame identifier
                            'component' => [
                                'head' => [
                                    'type' => 'head', // HTML head section
                                ],
                                'type'  => 'wrapper', // Root wrapper component type
                                'docEl' => [
                                    'tagName' => 'html', // Root HTML element
                                ],
                                // CSS properties that can be styled in the editor
                                'stylable' => [
                                    'background',
                                    'background-color',
                                    'background-image',
                                    'background-repeat',
                                    'background-attachment',
                                    'background-position',
                                    'background-size',
                                ],
                                'reference'  => 'wrapper-' . Str::random(7), // Unique reference for this wrapper
                                'components' => [], // Empty - components will be added later
                            ],
                        ],
                    ],
                ],
            ],
            'assets'      => [], // Media assets (images, videos, etc.)
            'styles'      => [], // Custom CSS styles
            'symbols'     => [], // Reusable component symbols
            'dataSources' => [], // External data source configurations
        ];
    }

    /**
     * Attach global blocks to the page structure
     * Moved from Page model to service
     */
    protected function attachGlobalBlocks(): array
    {
        $blockIds     = $this->pageBlockSettingRepository->getGlobalBlockIds();
        $globalBlocks = $this->pageBlockRepository->getGlobalBlocksByIds($blockIds->toArray());

        return $globalBlocks->map(function ($block) {
            return [
                'type'      => $block->reference,
                'block_id'  => $block->id,
                'droppable' => $block->droppable ?? 0,
                'reference' => $block->reference . '-' . Str::random(7),
            ];
        })->toArray();
    }
}
