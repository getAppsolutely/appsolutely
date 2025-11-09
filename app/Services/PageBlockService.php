<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GeneralPage;
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

    public function updateBlockSettingPublishStatus(int $settingId, ?string $publishedAt = null, ?string $expiredAt = null): int
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
    public function renderBlockSafely($block, GeneralPage $page): string
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
        $normalizeParameter = $this->normalizeParameterKeys($block->parameters);
        $parameters         = $this->getPossibleParameters($block->parameters, $normalizeParameter, $className);

        $parameters = array_merge($parameters, ['page' => $page->toArray()]);

        return Livewire::mount($className, $parameters, $reference);
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

    private function getPossibleParameters(array $originalParameters, array $normalisedParameters, string $className): array
    {
        // Assuming final parameters
        $parameters = $normalisedParameters;

        // Get all properties from Livewire
        $propertyKeys = $this->getArrayClassVars($className);

        // try to match normalised parameters
        $normalisedKeys         = array_unique(array_keys($normalisedParameters));
        $normalisedIntersection = array_intersect($normalisedKeys, $propertyKeys);
        if (empty($normalisedIntersection)) {
            $log                  = "{$className} properties are not in normalized parameters, trying to check if it is in original parameters. ";
            $originalKeys         = array_unique(array_keys($originalParameters));
            $originalIntersection = array_intersect($originalKeys, $propertyKeys);
            $possibleKeys         = $originalIntersection;

            if (empty($originalIntersection)) {
                $log .= "Not in original parameters either. guessing the key would be the first property of $className";
                $possibleKeys = ['data'];
            }
            local_debug($log, [
                'className'            => $className,
                'propertyKeys'         => $propertyKeys,
                'originalParameters'   => $originalParameters,
                'normalisedParameters' => $normalisedParameters]);
            $key        = \Arr::first($possibleKeys);
            $parameters = [$key => $originalParameters];
        }

        return $parameters;
    }

    private function getArrayClassVars(string $className): array
    {
        $vars = get_class_vars($className);

        return array_unique(array_keys(array_filter($vars, function ($value) {
            return is_array($value);
        })));
    }

    /**
     * Get HTML for block errors (only in debug mode)
     */
    private function getBlockErrorHtml(string $message): string
    {
        if (! config('app.debug')) {
            return ''; // Return empty string in production
        }

        // In debug mode, throw exception to show error details
        throw new \RuntimeException("Page block error: {$message}");
    }
}
