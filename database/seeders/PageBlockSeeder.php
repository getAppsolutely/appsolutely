<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\PageBlock;
use App\Models\PageBlockGroup;
use Illuminate\Database\Seeder;

final class PageBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create groups first
        $this->createGroups();

        // Then create blocks
        $layoutGroup  = PageBlockGroup::where('title', 'Navigation')->first();
        $contentGroup = PageBlockGroup::where('title', 'Content')->first();

        if ($layoutGroup) {
            $this->createHeaderFooterBlocks($layoutGroup);
        }

        if ($contentGroup) {
            $this->createContentBlocks($contentGroup);
        }
    }

    /**
     * Create page block groups.
     */
    private function createGroups(): void
    {
        $groups = [
            [
                'title'  => 'Navigation',
                'remark' => 'Navigation blocks like header and footer',
                'status' => Status::ACTIVE,
                'sort'   => 1,
            ],
            [
                'title'  => 'Content',
                'remark' => 'Content blocks for page sections',
                'status' => Status::ACTIVE,
                'sort'   => 2,
            ],
        ];

        foreach ($groups as $group) {
            PageBlockGroup::firstOrCreate(
                ['title' => $group['title']],
                $group
            );
        }
    }

    /**
     * Create header and footer blocks.
     */
    private function createHeaderFooterBlocks(PageBlockGroup $group): void
    {
        // Header
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\Header', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Header',
                    'class'       => 'App\\Livewire\\Header',
                    'template'    => $this->getTemplate('header'),
                    'description' => 'Main site header with navigation and logo',
                    'sort'        => 1,
                    'reference'   => 'header',
                    'scope'       => 'global',
                ]
            )
        );

        // Footer
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\Footer', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Footer',
                    'class'       => 'App\\Livewire\\Footer',
                    'template'    => $this->getTemplate('footer'),
                    'description' => 'Site footer with links, social media, and company information',
                    'sort'        => 2,
                    'reference'   => 'footer',
                    'scope'       => 'global',
                ]
            )
        );
    }

    /**
     * Create content blocks.
     */
    private function createContentBlocks(PageBlockGroup $group): void
    {
        // General Block
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\GeneralBlock', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'General Block',
                    'class'       => 'App\\Livewire\\GeneralBlock',
                    'template'    => $this->getTemplate('general-block'),
                    'description' => 'Base Livewire block.',
                    'sort'        => 1,
                    'reference'   => 'general-block',
                ]
            )
        );

        // Article List
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\ArticleList', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Article List',
                    'class'       => 'App\\Livewire\\ArticleList',
                    'template'    => $this->getTemplate('article-list'),
                    'description' => 'Display a list of articles with customizable layout and filtering options.',
                    'sort'        => 2,
                    'reference'   => 'article-list',
                ]
            )
        );

        // Dynamic Form
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\DynamicForm', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Dynamic Form',
                    'class'       => 'App\\Livewire\\DynamicForm',
                    'template'    => $this->getTemplate('dynamic-form'),
                    'description' => 'Database-driven form component that pulls form configuration from the database.',
                    'sort'        => 3,
                    'reference'   => 'dynamic-form',
                ]
            )
        );
    }

    /**
     * Get basic fields for page blocks.
     */
    private function getBasicFields(): array
    {
        return [
            'instruction' => null,
            'schema'      => [],
            'setting'     => [],
            'droppable'   => 0,
            'status'      => Status::ACTIVE,
            'scope'       => 'page',
        ];
    }

    /**
     * Get template for block.
     */
    private function getTemplate(string $reference): string
    {
        static $templates = null;
        if ($templates === null) {
            $path = __DIR__ . '/page_block_templates.json';
            if (file_exists($path)) {
                $json      = file_get_contents($path);
                $templates = json_decode($json, true);
            } else {
                $templates = [];
            }
        }

        return $templates[$reference]
            ?? '<div class="block-content"><div class="container"><div class="row"><div class="col-12"><h2>This is ' . htmlspecialchars($reference) . ' Block</h2><p>Block content goes here. Customize this template as needed.</p></div></div></div></div>';
    }
}
