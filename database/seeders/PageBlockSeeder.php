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
            ['class' => 'App\\Http\\Livewire\\Header', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Header',
                'class'       => 'App\\Http\\Livewire\\Header',
                'template'    => $this->getTemplate('header'),
                'description' => 'Main site header with navigation and logo',
                'sort'        => 1,
                'reference'   => 'header',
            ], $this->getBasicFields())
        );

        // Footer
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\Footer', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Footer',
                'class'       => 'App\\Http\\Livewire\\Footer',
                'template'    => $this->getTemplate('footer'),
                'description' => 'Site footer with links, social media, and company information',
                'sort'        => 2,
                'reference'   => 'footer',
            ], $this->getBasicFields())
        );
    }

    /**
     * Create content blocks.
     */
    private function createContentBlocks(PageBlockGroup $group): void
    {
        // Hero Banner (most commonly used)
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\HeroBanner', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Hero Banner',
                'class'       => 'App\\Http\\Livewire\\HeroBanner',
                'template'    => $this->getTemplate('hero-banner'),
                'description' => 'A prominent hero banner with headline, description, and call-to-action.',
                'sort'        => 1,
                'reference'   => 'hero-banner',
            ], $this->getBasicFields())
        );

        // Media Slider (supports both images and videos)
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\MediaSlider', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Media Slider',
                'class'       => 'App\\Http\\Livewire\\MediaSlider',
                'template'    => $this->getTemplate('media-slider'),
                'description' => 'A responsive slider that supports both images and videos with navigation controls.',
                'sort'        => 2,
                'reference'   => 'media-slider',
            ], $this->getBasicFields())
        );

        // Features Grid
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\FeaturesGrid', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Features Grid',
                'class'       => 'App\\Http\\Livewire\\FeaturesGrid',
                'template'    => $this->getTemplate('features-grid'),
                'description' => 'A grid layout to highlight product or service features.',
                'sort'        => 3,
                'reference'   => 'features-grid',
            ], $this->getBasicFields())
        );

        // Specifications
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\Specifications', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Specifications',
                'class'       => 'App\\Http\\Livewire\\Specifications',
                'template'    => $this->getTemplate('specifications'),
                'description' => 'Display specifications, technical details, or structured information in a table or list format.',
                'sort'        => 4,
                'reference'   => 'specifications',
            ], $this->getBasicFields())
        );

        // Video Showcase
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\VideoShowcase', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Video Showcase',
                'class'       => 'App\\Http\\Livewire\\VideoShowcase',
                'template'    => $this->getTemplate('video-showcase'),
                'description' => 'Showcase a featured video with supporting content.',
                'sort'        => 5,
                'reference'   => 'video-showcase',
            ], $this->getBasicFields())
        );

        // Photo Gallery
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\PhotoGallery', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Photo Gallery',
                'class'       => 'App\\Http\\Livewire\\PhotoGallery',
                'template'    => $this->getTemplate('photo-gallery'),
                'description' => 'A responsive photo gallery with lightbox support.',
                'sort'        => 6,
                'reference'   => 'photo-gallery',
            ], $this->getBasicFields())
        );

        // Customer Reviews
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\CustomerReviews', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Customer Reviews',
                'class'       => 'App\\Http\\Livewire\\CustomerReviews',
                'template'    => $this->getTemplate('customer-reviews'),
                'description' => 'Showcase customer reviews and ratings with profile images.',
                'sort'        => 7,
                'reference'   => 'customer-reviews',
            ], $this->getBasicFields())
        );

        // Testimonials
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\Testimonials', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Testimonials',
                'class'       => 'App\\Http\\Livewire\\Testimonials',
                'template'    => $this->getTemplate('testimonials'),
                'description' => 'Display customer testimonials and success stories with quotes.',
                'sort'        => 8,
                'reference'   => 'testimonials',
            ], $this->getBasicFields())
        );

        // Partners/Logos
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\PartnersLogos', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Partners/Logos',
                'class'       => 'App\\Http\\Livewire\\PartnersLogos',
                'template'    => $this->getTemplate('partners-logos'),
                'description' => 'Showcase client, partner, or sponsor logos in a grid layout.',
                'sort'        => 9,
                'reference'   => 'partners-logos',
            ], $this->getBasicFields())
        );

        // Achievement Stats
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\AchievementStats', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Achievement Stats',
                'class'       => 'App\\Http\\Livewire\\AchievementStats',
                'template'    => $this->getTemplate('achievement-stats'),
                'description' => 'Animated statistics to highlight achievements and milestones.',
                'sort'        => 10,
                'reference'   => 'achievement-stats',
            ], $this->getBasicFields())
        );

        // Team Showcase
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\TeamShowcase', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Team Showcase',
                'class'       => 'App\\Http\\Livewire\\TeamShowcase',
                'template'    => $this->getTemplate('team-showcase'),
                'description' => 'Showcase your team members with photos and bios.',
                'sort'        => 11,
                'reference'   => 'team-showcase',
            ], $this->getBasicFields())
        );

        // Company History
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\CompanyHistory', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Company History',
                'class'       => 'App\\Http\\Livewire\\CompanyHistory',
                'template'    => $this->getTemplate('company-history'),
                'description' => 'A timeline to present your company\'s history or project milestones.',
                'sort'        => 12,
                'reference'   => 'company-history',
            ], $this->getBasicFields())
        );

        // Social Media Feed
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\SocialMediaFeed', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Social Media Feed',
                'class'       => 'App\\Http\\Livewire\\SocialMediaFeed',
                'template'    => $this->getTemplate('social-media-feed'),
                'description' => 'Display recent posts from social media platforms like Instagram, Twitter, or Facebook.',
                'sort'        => 13,
                'reference'   => 'social-media-feed',
            ], $this->getBasicFields())
        );

        // Blog Posts
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\BlogPosts', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Blog Posts',
                'class'       => 'App\\Http\\Livewire\\BlogPosts',
                'template'    => $this->getTemplate('blog-posts'),
                'description' => 'Display a list or grid of recent blog posts or news articles.',
                'sort'        => 14,
                'reference'   => 'blog-posts',
            ], $this->getBasicFields())
        );

        // FAQ Section
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\FaqSection', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'FAQ Section',
                'class'       => 'App\\Http\\Livewire\\FaqSection',
                'template'    => $this->getTemplate('faq-section'),
                'description' => 'Frequently asked questions with expandable answers.',
                'sort'        => 15,
                'reference'   => 'faq-section',
            ], $this->getBasicFields())
        );

        // Pricing Plans
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\PricingPlans', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Pricing Plans',
                'class'       => 'App\\Http\\Livewire\\PricingPlans',
                'template'    => $this->getTemplate('pricing-plans'),
                'description' => 'Compare service or product pricing plans in a table.',
                'sort'        => 16,
                'reference'   => 'pricing-plans',
            ], $this->getBasicFields())
        );

        // Location Map
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\LocationMap', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Location Map',
                'class'       => 'App\\Http\\Livewire\\LocationMap',
                'template'    => $this->getTemplate('location-map'),
                'description' => 'Embed a Google Map to show your business location and contact info.',
                'sort'        => 17,
                'reference'   => 'location-map',
            ], $this->getBasicFields())
        );

        // Store Locations
        PageBlock::firstOrCreate(
            ['class' => 'App\\Http\\Livewire\\StoreLocations', 'block_group_id' => $group->id],
            array_merge([
                'title'       => 'Store Locations',
                'class'       => 'App\\Http\\Livewire\\StoreLocations',
                'template'    => $this->getTemplate('store-locations'),
                'description' => 'Display store locations with multiple layout options (grid, list, table) and optional map integration.',
                'sort'        => 18,
                'reference'   => 'store-locations',
            ], $this->getBasicFields())
        );
    }

    /**
     * Get basic fields for page blocks.
     */
    private function getBasicFields(): array
    {
        return [
            'instruction' => null,
            'schema'      => '{"_def_": null}',
            'setting'     => '{"_def_": null}',
            'droppable'   => 0,
            'status'      => 1,
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
