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

        $blockValue         = $block->blockValue ?? null;
        $viewName           = $blockValue?->view ?? '';
        $style              = $block->displayOptionsValue['style'] ?? '';
        $queryOptions       = $block->queryOptionsValue ?? [];
        $displayOptions     = $block->displayOptionsValue ?? [];
        $pageData           = $page->toArray();
        $pageData['blocks'] = $this->buildBlocksForAnchor($page);

        $data = [
            'page'           => $pageData,
            'viewName'       => $viewName,
            'style'          => $style,
            'queryOptions'   => $queryOptions,
            'displayOptions' => $displayOptions,
            'blockSort'      => (int) ($block->sort ?? 0),
            'blockReference' => $reference,
        ];

        return Livewire::mount($className, $data, $reference);
    }

    /**
     * Build minimal block data for anchor navigation (used by Anchor block).
     *
     * @return array<int, array{sort: int, reference: string, scope: string, view: string, display_options: array, block_title: string}>
     */
    private function buildBlocksForAnchor(GeneralPage $page): array
    {
        $blocks = $page->blocks ?? collect();

        return collect($blocks)->map(function ($b) {
            return [
                'sort'            => (int) ($b->sort ?? 0),
                'reference'       => (string) ($b->reference ?? ''),
                'scope'           => (string) ($b->block?->scope ?? 'page'),
                'view'            => (string) ($b->blockValue?->view ?? ''),
                'display_options' => $b->displayOptionsValue ?? [],
                'block_title'     => (string) ($b->block?->title ?? ''),
            ];
        })->values()->toArray();
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
