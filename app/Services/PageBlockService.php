<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PageBlockGroupRepository;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;

final class PageBlockService
{
    public function __construct(
        protected PageBlockGroupRepository $groupRepository,
        protected PageBlockRepository $blockRepository,
        protected PageBlockSettingRepository $settingRepository,
        protected PageBlockSchemaService $schemaService
    ) {}

    public function getCategorisedBlocks()
    {
        return $this->groupRepository->getCategorisedBlocks();
    }

    public function getPublishedBlockSettings(int $pageId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->settingRepository->getActivePublishedSettings($pageId);
    }

    public function updateBlockSettingPublishStatus(int $settingId, ?string $publishedAt = null, ?string $expiredAt = null): bool
    {
        return $this->settingRepository->updatePublishStatus($settingId, $publishedAt, $expiredAt);
    }

    /**
     * Get schema fields for a block
     */
    public function getSchemaFields(int $blockId): array
    {
        $block = $this->blockRepository->find($blockId);

        if (! $block) {
            return [];
        }

        $schema     = $this->schemaService->getBlockSchema($block);
        $formConfig = $this->schemaService->generateFormConfig($schema);

        return $formConfig;
    }

    /**
     * Validate and render a block safely
     * Returns the rendered HTML or error message
     */
    public function renderBlockSafely($block): string
    {
        // Validate block structure
        if (! isset($block['block']['class']) || ! isset($block['reference'])) {
            return $this->getBlockErrorHtml('Invalid block structure');
        }

        $className = $block['block']['class'];
        $reference = $block['reference'];

        // Validate class exists
        if (! class_exists($className)) {
            return $this->getBlockErrorHtml("Class '{$className}' not found");
        }

        // Validate reference is not empty
        if (empty($reference)) {
            return $this->getBlockErrorHtml('Reference is empty');
        }

        // Validate it's a Livewire component
        if (! is_subclass_of($className, Component::class)) {
            return $this->getBlockErrorHtml("Class '{$className}' is not a Livewire component");
        }

        // Get parameters safely and normalize keys
        $parameters = $this->normalizeParameterKeys($block->parameters ?? []);

        // Render the Livewire component
        try {
            return Livewire::mount($className, $parameters, $reference);
        } catch (\Exception $e) {
            return $this->getBlockErrorHtml('Error rendering block: ' . $e->getMessage());
        }
    }

    /**
     * Normalize parameter keys to camelCase (first layer only)
     */
    private function normalizeParameterKeys(array $parameters): array
    {
        return collect($parameters)
            ->mapWithKeys(fn ($value, $key) => [Str::camel($key) => $value])
            ->toArray();
    }

    /**
     * Get HTML for block errors (only in debug mode)
     */
    private function getBlockErrorHtml(string $message): string
    {
        if (! config('app.debug')) {
            return ''; // Return empty string in production
        }

        throw new \Exception($message);
    }
}
