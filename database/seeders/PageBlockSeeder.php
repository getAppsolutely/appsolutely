<?php

declare(strict_types=1);

namespace Database\Seeders;

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
                'status' => 1,
                'sort'   => 1,
            ],
            [
                'title'  => 'Content',
                'remark' => 'Content blocks for page sections',
                'status' => 1,
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
                    'title'         => 'Header',
                    'class'         => 'App\\Livewire\\Header',
                    'template'      => $this->getTemplate('header'),
                    'description'   => 'Main site header with navigation and logo',
                    'sort'          => 1,
                    'reference'     => 'global',
                    'schema_values' => [
                        'main_menu' => 'main-nav',
                    ],
                ]
            )
        );

        // Footer
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\Footer', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'         => 'Footer',
                    'class'         => 'App\\Livewire\\Footer',
                    'template'      => $this->getTemplate('footer'),
                    'description'   => 'Site footer with links, social media, and company information',
                    'sort'          => 2,
                    'reference'     => 'footer',
                    'scope'         => 'global',
                    'schema_values' => [
                        'footer_menu' => 'main-nav',
                    ],
                ]
            )
        );
    }

    /**
     * Create content blocks.
     */
    private function createContentBlocks(PageBlockGroup $group): void
    {
        // Hero Banner (most commonly used)
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\HeroBanner', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Hero Banner',
                    'class'       => 'App\\Livewire\\HeroBanner',
                    'template'    => $this->getTemplate('hero-banner'),
                    'description' => 'A prominent hero banner with headline, description, and call-to-action.',
                    'sort'        => 1,
                    'reference'   => 'hero-banner',
                ]
            )
        );

        // Media Slider (supports both images and videos)
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\MediaSlider', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Media Slider',
                    'class'       => 'App\\Livewire\\MediaSlider',
                    'template'    => $this->getTemplate('media-slider'),
                    'description' => 'A responsive slider that supports both images and videos with navigation controls.',
                    'sort'        => 2,
                    'reference'   => 'media-slider',
                ]
            )
        );

        // Features Grid
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\FeaturesGrid', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Features Grid',
                    'class'       => 'App\\Livewire\\FeaturesGrid',
                    'template'    => $this->getTemplate('features-grid'),
                    'description' => 'A grid layout to highlight product or service features.',
                    'sort'        => 3,
                    'reference'   => 'features-grid',
                ]
            )
        );

        // Specifications
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\Specifications', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Specifications',
                    'class'       => 'App\\Livewire\\Specifications',
                    'template'    => $this->getTemplate('specifications'),
                    'description' => 'Display specifications, technical details, or structured information in a table or list format.',
                    'sort'        => 4,
                    'reference'   => 'specifications',
                ]
            )
        );

        // Video Showcase
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\VideoShowcase', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Video Showcase',
                    'class'       => 'App\\Livewire\\VideoShowcase',
                    'template'    => $this->getTemplate('video-showcase'),
                    'description' => 'Showcase a featured video with supporting content.',
                    'sort'        => 5,
                    'reference'   => 'video-showcase',
                ]
            )
        );

        // Photo Gallery
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\PhotoGallery', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Photo Gallery',
                    'class'       => 'App\\Livewire\\PhotoGallery',
                    'template'    => $this->getTemplate('photo-gallery'),
                    'description' => 'A responsive photo gallery with lightbox support.',
                    'sort'        => 6,
                    'reference'   => 'photo-gallery',
                ]
            )
        );

        // Customer Reviews
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\CustomerReviews', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Customer Reviews',
                    'class'       => 'App\\Livewire\\CustomerReviews',
                    'template'    => $this->getTemplate('customer-reviews'),
                    'description' => 'Showcase customer reviews and ratings with profile images.',
                    'sort'        => 7,
                    'reference'   => 'customer-reviews',
                ]
            )
        );

        // Testimonials
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\Testimonials', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Testimonials',
                    'class'       => 'App\\Livewire\\Testimonials',
                    'template'    => $this->getTemplate('testimonials'),
                    'description' => 'Display customer testimonials and success stories with quotes.',
                    'sort'        => 8,
                    'reference'   => 'testimonials',
                ]
            )
        );

        // Partners/Logos
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\PartnersLogos', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Partners/Logos',
                    'class'       => 'App\\Livewire\\PartnersLogos',
                    'template'    => $this->getTemplate('partners-logos'),
                    'description' => 'Showcase client, partner, or sponsor logos in a grid layout.',
                    'sort'        => 9,
                    'reference'   => 'partners-logos',
                ]
            )
        );

        // Achievement Stats
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\AchievementStats', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Achievement Stats',
                    'class'       => 'App\\Livewire\\AchievementStats',
                    'template'    => $this->getTemplate('achievement-stats'),
                    'description' => 'Animated statistics to highlight achievements and milestones.',
                    'sort'        => 10,
                    'reference'   => 'achievement-stats',
                ]
            )
        );

        // Team Showcase
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\TeamShowcase', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Team Showcase',
                    'class'       => 'App\\Livewire\\TeamShowcase',
                    'template'    => $this->getTemplate('team-showcase'),
                    'description' => 'Showcase your team members with photos and bios.',
                    'sort'        => 11,
                    'reference'   => 'team-showcase',
                ]
            )
        );

        // Company History
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\CompanyHistory', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Company History',
                    'class'       => 'App\\Livewire\\CompanyHistory',
                    'template'    => $this->getTemplate('company-history'),
                    'description' => 'A timeline to present your company\'s history or project milestones.',
                    'sort'        => 12,
                    'reference'   => 'company-history',
                ]
            )
        );

        // Social Media Feed
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\SocialMediaFeed', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Social Media Feed',
                    'class'       => 'App\\Livewire\\SocialMediaFeed',
                    'template'    => $this->getTemplate('social-media-feed'),
                    'description' => 'Display recent posts from social media platforms like Instagram, Twitter, or Facebook.',
                    'sort'        => 13,
                    'reference'   => 'social-media-feed',
                ]
            )
        );

        // Blog Posts
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\BlogPosts', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Blog Posts',
                    'class'       => 'App\\Livewire\\BlogPosts',
                    'template'    => $this->getTemplate('blog-posts'),
                    'description' => 'Display a list or grid of recent blog posts or news articles.',
                    'sort'        => 14,
                    'reference'   => 'blog-posts',
                ]
            )
        );

        // FAQ Section
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\FaqSection', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'FAQ Section',
                    'class'       => 'App\\Livewire\\FaqSection',
                    'template'    => $this->getTemplate('faq-section'),
                    'description' => 'Frequently asked questions with expandable answers.',
                    'sort'        => 15,
                    'reference'   => 'faq-section',
                ]
            )
        );

        // Pricing Plans
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\PricingPlans', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Pricing Plans',
                    'class'       => 'App\\Livewire\\PricingPlans',
                    'template'    => $this->getTemplate('pricing-plans'),
                    'description' => 'Compare service or product pricing plans in a table.',
                    'sort'        => 16,
                    'reference'   => 'pricing-plans',
                ]
            )
        );

        // Location Map
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\LocationMap', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Location Map',
                    'class'       => 'App\\Livewire\\LocationMap',
                    'template'    => $this->getTemplate('location-map'),
                    'description' => 'Embed a Google Map to show your business location and contact info.',
                    'sort'        => 17,
                    'reference'   => 'location-map',
                ]
            )
        );

        // Store Locations
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\StoreLocations', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Store Locations',
                    'class'       => 'App\\Livewire\\StoreLocations',
                    'template'    => $this->getTemplate('store-locations'),
                    'description' => 'Display store locations with multiple layout options (grid, list, table) and optional map integration.',
                    'sort'        => 18,
                    'reference'   => 'store-locations',
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
                    'sort'        => 19,
                    'reference'   => 'dynamic-form',
                ]
            )
        );

        // Transition Section
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\TransitionSection', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Transition Section',
                    'class'       => 'App\\Livewire\\TransitionSection',
                    'template'    => $this->getTemplate('transition-section'),
                    'description' => 'Visual transition section with full-width background image, perfect for bridging between different content sections.',
                    'sort'        => 20,
                    'reference'   => 'transition-section',
                ]
            )
        );

        // Text Block
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\TextDocument', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Text Document',
                    'class'       => 'App\\Livewire\\TextDocument',
                    'template'    => $this->getTemplate('text-document'),
                    'description' => 'Simple text content block for documents like Terms of Service, Privacy Policy, etc.',
                    'sort'        => 21,
                    'reference'   => 'text-document',
                ]
            )
        );

        // Article List Block
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\ArticleList', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Article List',
                    'class'       => 'App\\Livewire\\ArticleList',
                    'template'    => $this->getTemplate('article-list'),
                    'description' => 'Display a list of articles with customizable layout and filtering options.',
                    'sort'        => 22,
                    'reference'   => 'article-list',
                ]
            )
        );

        // Product Variant Block
        PageBlock::firstOrCreate(
            ['class' => 'App\\Livewire\\ProductVariantBlock', 'block_group_id' => $group->id],
            array_merge(
                $this->getBasicFields(),
                [
                    'title'       => 'Product Variant Block',
                    'class'       => 'App\\Livewire\\ProductVariantBlock',
                    'template'    => $this->getTemplate('product-variant-block'),
                    'description' => 'Display product variants with configuration switching, color selection, specifications, pricing, and call-to-action buttons. Perfect for vehicle or product detail pages.',
                    'sort'        => 23,
                    'reference'   => 'product-variant-block',
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
            'status'      => 1,
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
