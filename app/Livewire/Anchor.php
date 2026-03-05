<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\BlockScope;
use Illuminate\Contracts\Container\Container;

/**
 * Anchor block: sticky navigation bar linking to sections below.
 *
 * Shows only page-scoped blocks after itself (excludes global blocks like footer).
 * Only includes blocks that have anchor_label in displayOptions.
 */
final class Anchor extends GeneralBlock
{
    public int $blockSort = 0;

    /**
     * @var array<int, array{reference: string, title: string}>
     */
    public array $anchorItems = [];

    protected function initializeComponent(Container $container): void
    {
        $blocks = $this->page['blocks'] ?? [];
        if (empty($blocks) || $this->blockSort <= 0) {
            $this->anchorItems = [];

            return;
        }

        $items = [];

        foreach ($blocks as $block) {
            $sort  = (int) ($block['sort'] ?? 0);
            $scope = $block['scope'] ?? BlockScope::Page->value;

            if ($scope !== BlockScope::Page->value || $sort <= $this->blockSort) {
                continue;
            }

            $reference      = (string) ($block['reference'] ?? '');
            $displayOptions = $block['display_options'] ?? [];

            $title = $this->resolveTitle($displayOptions);

            if ($title !== '' && $reference !== '') {
                $items[] = [
                    'reference' => $reference,
                    'title'     => $title,
                ];
            }
        }

        $this->anchorItems = $items;
    }

    /**
     * Resolve anchor label. Returns empty string if block has no anchor_label in displayOptions.
     */
    private function resolveTitle(array $displayOptions): string
    {
        return trim((string) ($displayOptions['anchor_label'] ?? ''));
    }

    protected function getExtraData(): array
    {
        return [
            'anchorItems' => $this->anchorItems,
        ];
    }
}
