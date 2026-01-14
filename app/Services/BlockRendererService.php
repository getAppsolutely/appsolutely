<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GeneralPage;
use App\Services\Contracts\BlockRendererServiceInterface;
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

        $className = ! empty($block['block']['class']) ? $block['block']['class'] : 'App\Livewire\GeneralBlock';
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

        $viewName       = $block->blockValue->view ?? '';
        $style          = $block->displayOptionsValue['style'] ?? '';
        $queryOptions   = $block->queryOptionsValue ?? [];
        $displayOptions = $block->displayOptionsValue ?? [];

        $data = [
            'page'           => $page->toArray(),
            'viewName'       => $viewName,
            'style'          => $style,
            'queryOptions'   => $queryOptions,
            'displayOptions' => $displayOptions,
        ];

        return Livewire::mount($className, $data, $reference);
    }

    /**
     * Get HTML for block errors (only in debug mode)
     */
    private function getBlockErrorHtml(string $message): string
    {
        if (! app()->isProduction()) {
            return "<div class='alert alert-danger'><strong>Block Error:</strong> {$message}</div>";
        }

        return '';
    }
}
