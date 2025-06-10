<?php

namespace App\Admin\Controllers\Api;

use App\Models\Page;
use App\Services\PageBlockService;
use App\Services\PageService;
use Illuminate\Http\Request;

class PageBuilderAdminApiController extends AdminBaseApiController
{
    public function __construct(protected PageService $pageService, protected PageBlockService $pageBlockService) {}

    /**
     * Get page data for the builder
     */
    public function getPageData(Request $request, string $reference)
    {
        $page = $this->pageService->findByReference($reference);

        return $this->success([
            'page' => $page,
        ]);
    }

    /**
     * Save page builder data
     */
    public function savePageData(Request $request, string $reference)
    {
        $data = $request->get('data');
        if (empty($data)) {
            return $this->error('Page data cannot be empty.');
        }

        $page = $this->pageService->findByReference($reference);
        $page->update(['content' => $data]);

        return $this->success($data, 'Page saved successfully.');
    }

    /**
     * Reset page builder data
     */
    public function resetPageData(Request $request, string $reference)
    {
        $page = $this->pageService->resetPageContent($reference);

        return $this->success(['page' => $page], __t('Page content has been reset.'));
    }

    /**
     * Get available blocks registry
     */
    public function getBlockRegistry()
    {
        $data = [
            [
                'name'   => '内容区域',
                'icon'   => 'fas fa-align-left',
                'sort'   => 1,
                'blocks' => [
                    [
                        'type'      => 'feature',
                        'label'     => '特性展示',
                        'desc'      => '展示产品特性或服务优势',
                        'sort'      => 1,
                        'content'   => '',
                        'style'     => ['background-color' => '#f9fafb', 'padding' => '20px'],
                        'tagName'   => 'section',
                        'droppable' => false,
                        'traits'    => [
                            [
                                'type'        => 'text',
                                'name'        => 'category',
                                'label'       => 'Category',
                                'placeholder' => 'news',
                            ],
                            [
                                'type'        => 'number',
                                'name'        => 'limit',
                                'label'       => 'Limit',
                                'placeholder' => '5',
                                'min'         => 0,
                                'max'         => 100,
                                'step'        => 5,
                            ],
                            [
                                'type'        => 'checkbox',
                                'name'        => 'gender',
                                'label'       => 'Gender',
                                'placeholder' => 'n/a',
                                'valueTrue'   => 'Yes',
                                'no'          => 'No',
                            ],
                            [
                                'type'        => 'select',
                                'name'        => 'options',
                                'label'       => 'Options',
                                'placeholder' => 'news',
                                'options'     => [
                                    ['id' => 'opt1', 'label' => 'Option 1'],
                                    ['id' => 'opt2', 'label' => 'Option 2'],
                                ],
                            ],
                            [
                                'type'        => 'color',
                                'name'        => 'color',
                                'label'       => 'Color',
                                'placeholder' => 'color?',
                            ],
                            [
                                'type'        => 'text',
                                'name'        => 'category',
                                'label'       => 'Category',
                                'placeholder' => 'news',
                            ],
                        ],
                    ],
                    [
                        'type'      => 'testimonial',
                        'label'     => '客户评价',
                        'desc'      => '展示客户推荐和评价',
                        'sort'      => 2,
                        'content'   => '<blockquote><p>客户非常满意！</p><h2>Kent</h2><p>Excellent!</p></blockquote>',
                        'style'     => ['border-left' => '4px solid #ccc', 'padding' => '10px'],
                        'tagName'   => 'blockquote',
                        'droppable' => false,
                        'traits'    => [
                            [
                                'type'        => 'text',
                                'name'        => 'type',
                                'label'       => 'Category',
                                'placeholder' => 'news',
                            ],
                            [
                                'type'        => 'limit',
                                'name'        => 'limit',
                                'label'       => 'Limit',
                                'placeholder' => '5',
                                'min'         => 0,
                                'max'         => 100,
                                'step'        => 5,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $data = $this->pageBlockService->getCategorisedBlocks()->toArray();

        return $this->success($data);
    }
}
