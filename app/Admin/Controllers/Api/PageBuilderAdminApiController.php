<?php

namespace App\Admin\Controllers\Api;

use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Http\Request;

class PageBuilderAdminApiController extends AdminBaseApiController
{
    protected PageRepository $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * Get page data for the builder
     */
    public function getPageData(Request $request, int $pageId)
    {
        $page = $this->pageRepository->findOrFail($pageId);

        return $this->success([
            'page'       => $page,
            'components' => $page->builder_data ?? [],
        ]);
    }

    /**
     * Save page builder data
     */
    public function savePageData(Request $request, int $pageId)
    {
        $page = $this->pageRepository->findOrFail($pageId);

        $validated = $request->validate([
            'components' => 'array',
            'settings'   => 'array',
        ]);

        $page->update([
            'builder_data'     => $validated['components'] ?? [],
            'builder_settings' => $validated['settings'] ?? [],
        ]);

        return $this->success(null, 'Page saved successfully');
    }

    /**
     * Get available components registry
     */
    public function getComponentsRegistry()
    {
        // For now, return a basic set of components
        // Later this will be dynamic from database/config
        return $this->success([
            'categories' => [
                [
                    'id'         => 'layout',
                    'name'       => 'Layout',
                    'components' => [
                        [
                            'id'            => 'vertical',
                            'name'          => 'Vertical Layout',
                            'icon'          => '',
                            'preview'       => '/images/components/vertical.png',
                            'config_schema' => [
                                'background_color' => ['type' => 'color', 'default' => '#ffffff'],
                                'padding'          => ['type' => 'spacing', 'default' => 'medium'],
                            ],
                        ],
                        [
                            'id'            => 'horizontal',
                            'name'          => 'Horizontal Layout',
                            'icon'          => '',
                            'preview'       => '/images/components/horizontal.png',
                            'config_schema' => [
                                'columns' => ['type' => 'number', 'default' => 2, 'min' => 1, 'max' => 6],
                                'gap'     => ['type' => 'spacing', 'default' => 'medium'],
                            ],
                        ],
                    ],
                ],
                [
                    'id'         => 'content',
                    'name'       => 'Blocks',
                    'components' => [
                        [
                            'id'            => 'heading',
                            'name'          => 'Heading',
                            'icon'          => 'type',
                            'preview'       => '/images/components/heading.png',
                            'config_schema' => [
                                'text'      => ['type' => 'text', 'default' => 'Your Heading'],
                                'level'     => ['type' => 'select', 'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], 'default' => 'h2'],
                                'alignment' => ['type' => 'select', 'options' => ['left', 'center', 'right'], 'default' => 'left'],
                            ],
                        ],
                        [
                            'id'            => 'text',
                            'name'          => 'Text Block',
                            'icon'          => 'file-text',
                            'preview'       => '/images/components/text.png',
                            'config_schema' => [
                                'content'   => ['type' => 'textarea', 'default' => 'Your text content here...'],
                                'alignment' => ['type' => 'select', 'options' => ['left', 'center', 'right'], 'default' => 'left'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
