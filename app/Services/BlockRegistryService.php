<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PageBlockGroup;
use App\Repositories\PageBlockRepository;
use App\Services\Contracts\ManifestServiceInterface;
use Qirolab\Theme\Theme;

/**
 * Builds block registry from theme manifest.json, matching blocks to page_block by class
 * to obtain block_id for correct saving of page design data.
 *
 * The manifest is the source of truth for available blocks per theme.
 * page_block lookup by class provides block_id and grouping.
 */
final readonly class BlockRegistryService
{
    private const DEFAULT_TAG_NAME = 'section';

    private const DEFAULT_PLACEHOLDER = '<section class="block-placeholder"><div class="container"><p>Block content</p></div></section>';

    public function __construct(
        protected ManifestServiceInterface $manifestService,
        protected PageBlockRepository $blockRepository
    ) {}

    /**
     * Get block registry from active theme manifest, with block_id from page_block lookup.
     *
     * @return array<int, array<string, mixed>> Groups with blocks, same schema as getCategorisedBlocks
     */
    public function getRegistry(?string $themeName = null): array
    {
        $themeName = $themeName ?? Theme::active();
        $manifest  = $this->manifestService->loadManifest($themeName);
        $templates = $manifest['templates'] ?? [];

        if (empty($templates)) {
            return [];
        }

        $blocksByClass = $this->blockRepository->getBlocksByClass();

        $registryBlocks = [];
        foreach ($templates as $manifestRef => $config) {
            $component = $config['component'] ?? null;
            if (empty($component) || ! is_string($component)) {
                continue;
            }

            $pageBlock = $blocksByClass[$component] ?? null;
            if ($pageBlock === null) {
                continue;
            }

            if (! class_exists($component)) {
                continue;
            }

            $registryBlocks[] = [
                'page_block'   => $pageBlock,
                'manifest_ref' => $manifestRef,
                'label'        => $config['label'] ?? $pageBlock->title,
                'description'  => $config['description'] ?? $pageBlock->description ?? '',
            ];
        }

        return $this->groupAndFormat($registryBlocks);
    }

    /**
     * @param  array<int, array{page_block: \App\Models\PageBlock, manifest_ref: string, label: string, description: string}>  $registryBlocks
     * @return array<int, array<string, mixed>>
     */
    private function groupAndFormat(array $registryBlocks): array
    {
        $grouped = collect($registryBlocks)->groupBy(fn (array $item) => $item['page_block']->block_group_id);

        $groupIds = $grouped->keys()->filter()->values()->toArray();
        if (empty($groupIds)) {
            return [];
        }

        $groups = PageBlockGroup::query()
            ->whereIn('id', $groupIds)
            ->status()
            ->orderBy('sort')
            ->get();

        $result = [];
        foreach ($groups as $group) {
            $items  = $grouped->get($group->id, collect());
            $blocks = $items->map(function (array $item) {
                $block = $item['page_block'];
                $arr   = $block->toArray();

                return array_merge($arr, [
                    'label'   => $item['label'],
                    'type'    => $item['manifest_ref'],
                    'content' => $block->template ?: self::DEFAULT_PLACEHOLDER,
                    'tagName' => self::DEFAULT_TAG_NAME,
                ]);
            })->sortBy(fn (array $b) => $b['sort'] ?? 0)->values()->all();

            if (empty($blocks)) {
                continue;
            }

            $result[] = [
                'id'         => $group->id,
                'title'      => $group->title,
                'remark'     => $group->remark,
                'sort'       => $group->sort,
                'status'     => $group->status,
                'created_at' => $group->created_at?->toIso8601String(),
                'updated_at' => $group->updated_at?->toIso8601String(),
                'blocks'     => $blocks,
            ];
        }

        return $result;
    }
}
