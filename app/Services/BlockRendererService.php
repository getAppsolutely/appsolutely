<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GeneralPage;
use App\Services\Contracts\BlockRendererServiceInterface;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;

final readonly class BlockRendererService implements BlockRendererServiceInterface
{
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

    /**
     * Get possible parameters by matching with component properties
     */
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

    /**
     * Get array-type class variables from a class
     */
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
        if (app()->environment() !== 'production') {
            return "<div class='alert alert-danger'><strong>Block Error:</strong> {$message}</div>";
        }

        return '';
    }
}
