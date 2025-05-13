<?php

namespace App\Services;

use App\Models\Page;
use App\Models\PageContainerComponent;
use App\Repositories\PageRepository;

class PageService
{
    /**
     * List of available components and their styles
     */
    private const AVAILABLE_COMPONENTS = [
        'hero-banner' => [
            'name'   => 'Hero Banner',
            'styles' => ['default', 'classic', 'modern'],
        ],
        'hero-slider' => [
            'name'   => 'Hero Slider',
            'styles' => ['default', 'fullscreen'],
        ],
        'announcement-bar' => [
            'name'   => 'Announcement Bar',
            'styles' => ['default', 'sticky'],
        ],
        'video-hero' => [
            'name'   => 'Video Hero',
            'styles' => ['default', 'autoplay'],
        ],
        'testimonials' => [
            'name'   => 'Testimonials',
            'styles' => ['default', 'grid', 'carousel'],
        ],
        'faqs-accordion' => [
            'name'   => 'FAQs Accordion',
            'styles' => ['default', 'grouped'],
        ],
    ];

    public function __construct(
        private PageRepository $pageRepository
    ) {}

    public function getPublishedPage(string $slug): ?Page
    {
        return $this->pageRepository->findPublishedBySlug($slug);
    }

    public function getAvailableComponents(): array
    {
        return self::AVAILABLE_COMPONENTS;
    }

    public function isValidComponent(string $componentName): bool
    {
        return isset(self::AVAILABLE_COMPONENTS[$componentName]);
    }

    public function getComponentStyles(string $componentName): array
    {
        return self::AVAILABLE_COMPONENTS[$componentName]['styles'] ?? ['default'];
    }

    public function renderComponent(PageContainerComponent $component): string
    {
        if (! $this->isValidComponent($component->component_name)) {
            return '<!-- Invalid component -->';
        }

        $componentPath = sprintf(
            'sections.%s.%s',
            $component->component_name,
            $component->style ?? 'default'
        );

        return view($componentPath, [
            'component' => $component,
            'config'    => $component->config,
            'html'      => $component->html,
            'layout'    => $component->layout,
        ])->render();
    }
}
