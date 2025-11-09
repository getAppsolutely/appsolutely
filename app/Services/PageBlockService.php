<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GeneralPage;
use App\Repositories\PageBlockGroupRepository;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;
use App\Services\Contracts\PageBlockSchemaServiceInterface;
use App\Services\Contracts\PageBlockServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;

final readonly class PageBlockService implements PageBlockServiceInterface
{
    public function __construct(
        protected PageBlockGroupRepository $groupRepository,
        protected PageBlockRepository $blockRepository,
        protected PageBlockSettingRepository $settingRepository,
        protected PageBlockSchemaServiceInterface $schemaService
    ) {}

    public function getCategorisedBlocks(): Collection
    {
        return $this->groupRepository->getCategorisedBlocks();
    }

    public function getPublishedBlockSettings(int $pageId): Collection
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
        // Start with normalised parameters as the default
        $parameters = $normalisedParameters;

        // Get all array-type properties from the Livewire component class
        // These are the properties that can accept parameter data
        $propertyKeys = $this->getArrayClassVars($className);

        // Step 1: Try to match normalised parameter keys with component properties
        $normalisedKeys         = array_unique(array_keys($normalisedParameters));
        $normalisedIntersection = array_intersect($normalisedKeys, $propertyKeys);

        // If no match found in normalised parameters, try original parameters
        if (empty($normalisedIntersection)) {
            $log                  = "{$className} properties are not in normalized parameters, trying to check if it is in original parameters. ";
            $originalKeys         = array_unique(array_keys($originalParameters));
            $originalIntersection = array_intersect($originalKeys, $propertyKeys);
            $possibleKeys         = $originalIntersection;

            // If still no match, fallback to 'data' key as default
            // This handles cases where parameters don't match any component property
            if (empty($originalIntersection)) {
                $log .= "Not in original parameters either. guessing the key would be the first property of $className";
                $possibleKeys = ['data'];
            }

            // Log the mismatch for debugging (only in debug mode)
            local_debug($log, [
                'className'            => $className,
                'propertyKeys'         => $propertyKeys,
                'originalParameters'   => $originalParameters,
                'normalisedParameters' => $normalisedParameters]);

            // Use the first matching key (or 'data' fallback) and wrap original parameters
            $key        = \Arr::first($possibleKeys);
            $parameters = [$key => $originalParameters];
        }

        return $parameters;
    }

    private function getArrayClassVars(string $className): array
    {
        // Get all class variables (properties) from the Livewire component
        $vars = get_class_vars($className);

        // Filter to only return property names that have array-type default values
        // This identifies which properties can accept array parameters
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
