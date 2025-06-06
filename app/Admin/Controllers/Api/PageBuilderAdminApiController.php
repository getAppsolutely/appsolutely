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
            'page' => $page,
        ]);
    }

    /**
     * Save page builder data
     */
    public function savePageData(Request $request, int $pageId)
    {
        $data = $request->get('data');
        if (empty($data)) {
            return $this->error('Page data cannot be empty.');
        }

        $page = $this->pageRepository->findOrFail($pageId);
        $page->update(['content' => $data]);

        return $this->success($data, 'Page saved successfully.');
    }

    /**
     * Get available blocks registry
     */
    public function getBlocksRegistry()
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
                        'content'   => '<section><h2>产品特性</h2><p>这里是特色介绍</p><p>更多内容</p><p>更多内容</p><p>更多内容</p></section>',
                        'style'     => ['background-color' => '#f9fafb', 'padding' => '20px'],
                        'tagName'   => 'section',
                        'droppable' => false,
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
                    ],
                ],
            ],
            [
                'name'   => '页眉页脚',
                'icon'   => 'fas fa-heading',
                'sort'   => 2,
                'blocks' => [
                    [
                        'type'      => 'header',
                        'label'     => '页眉',
                        'desc'      => '网站顶部导航',
                        'sort'      => 1,
                        'content'   => '<header><h1>品牌名</h1></header>',
                        'style'     => ['background' => '#e0f2fe', 'padding' => '20px'],
                        'tagName'   => 'header',
                        'droppable' => false,
                    ],
                    [
                        'type'      => 'footer',
                        'label'     => '页脚',
                        'desc'      => '网站底部版权信息',
                        'sort'      => 2,
                        'content'   => '<footer><p>© 2025 公司名</p></footer>',
                        'style'     => ['background' => '#f3f4f6', 'padding' => '20px'],
                        'tagName'   => 'footer',
                        'droppable' => false,
                    ],
                ],
            ],
        ];

        return $this->success($data);
    }
}
