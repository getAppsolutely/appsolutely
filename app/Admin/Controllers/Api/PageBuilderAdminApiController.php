<?php

namespace App\Admin\Controllers\Api;

use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Http\Request;

class PageBuilderAdminApiController extends AdminBaseApiController
{
    public function __construct(protected PageRepository $pageRepository) {}

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
        $data = [
            [
                'name'       => '内容区域',
                'icon'       => 'fas fa-align-left',
                'sort'       => 1,
                'components' => [
                    [
                        'type'    => 'feature',
                        'label'   => '特性展示',
                        'desc'    => '展示产品特性或服务优势',
                        'sort'    => 1,
                        'content' => '<section><h2>产品特性</h2><p>这里是特色介绍</p></section>',
                        'style'   => ['background-color' => '#f9fafb', 'padding' => '20px'],
                        'tagName' => 'section',
                    ],
                    [
                        'type'    => 'testimonial',
                        'label'   => '客户评价',
                        'desc'    => '展示客户推荐和评价',
                        'sort'    => 2,
                        'content' => '<blockquote><p>客户非常满意！</p></blockquote>',
                        'style'   => ['border-left' => '4px solid #ccc', 'padding' => '10px'],
                        'tagName' => 'blockquote',
                    ],
                ],
            ],
            [
                'name'       => '页眉页脚',
                'icon'       => 'fas fa-heading',
                'sort'       => 2,
                'components' => [
                    [
                        'type'    => 'header',
                        'label'   => '页眉',
                        'desc'    => '网站顶部导航',
                        'sort'    => 1,
                        'content' => '<header><h1>品牌名</h1></header>',
                        'style'   => ['background' => '#e0f2fe', 'padding' => '20px'],
                        'tagName' => 'header',
                    ],
                    [
                        'type'    => 'footer',
                        'label'   => '页脚',
                        'desc'    => '网站底部版权信息',
                        'sort'    => 2,
                        'content' => '<footer><p>© 2025 公司名</p></footer>',
                        'style'   => ['background' => '#f3f4f6', 'padding' => '20px'],
                        'tagName' => 'footer',
                    ],
                ],
            ],
        ];

        return $this->success($data);
    }
}
