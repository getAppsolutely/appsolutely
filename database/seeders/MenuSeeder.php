<?php

namespace Database\Seeders;

use App\Enums\MenuTarget;
use App\Enums\MenuType;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create menu first
        $this->createRoots();

        // Then create menu items
        $mainRoot   = Menu::where('title', 'Main Navigation')->first();
        $policyRoot = Menu::where('title', 'Policy menu')->first();
        $socialRoot = Menu::where('title', 'Social Media')->first();

        if ($mainRoot) {
            $this->createMainNavigation($mainRoot);
        }

        if ($policyRoot) {
            $this->createPolicyMenu($policyRoot);
        }

        if ($socialRoot) {
            $this->createSocialMediaMenu($socialRoot);
        }
    }

    private function createRoots(): void
    {
        $groups = [
            [
                'title'     => 'Main Navigation',
                'reference' => 'main-nav',
                'remark'    => 'Primary navigation menu for the website',
                'status'    => 1,
            ],
            [
                'title'     => 'Footer Menu',
                'reference' => 'footer-menu',
                'remark'    => 'Footer navigation links',
                'status'    => 1,
            ],
            [
                'title'     => 'User Menu',
                'reference' => 'user-menu',
                'remark'    => 'User-related links',
                'status'    => 1,
            ],
            [
                'title'     => 'Social Media',
                'reference' => 'social-media',
                'remark'    => 'Social media navigation links',
                'status'    => 1,
            ],
            [
                'title'     => 'Policy menu',
                'reference' => 'policy-menu',
                'remark'    => 'Policy links',
                'status'    => 1,
            ],
        ];

        foreach ($groups as $group) {
            Menu::firstOrCreate(
                ['reference' => $group['reference']],
                $group
            );
        }
    }

    private function createMainNavigation(Menu $parent): void
    {
        // Home
        $home = Menu::firstOrCreate(
            ['title' => 'Home', 'parent_id' => $parent->id],
            [
                'title'       => 'Home',
                'parent_id'   => $parent->id,
                'url'         => '/',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // About
        $about = Menu::firstOrCreate(
            ['title' => 'About', 'parent_id' => $parent->id],
            [
                'title'       => 'About',
                'parent_id'   => $parent->id,
                'url'         => '/about',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Services dropdown
        $services = Menu::firstOrCreate(
            ['title' => 'Services', 'parent_id' => $parent->id],
            [
                'title'       => 'Services',
                'parent_id'   => $parent->id,
                'url'         => '/services',
                'type'        => MenuType::Dropdown->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Service sub-items
        Menu::firstOrCreate(
            ['title' => 'Web Development', 'parent_id' => $services->id],
            [
                'title'       => 'Web Development',
                'parent_id'   => $services->id,
                'url'         => '/services/web-development',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        Menu::firstOrCreate(
            ['title' => 'Mobile Apps', 'parent_id' => $services->id],
            [
                'title'       => 'Mobile Apps',
                'parent_id'   => $services->id,
                'url'         => '/services/mobile-apps',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Contact
        Menu::firstOrCreate(
            ['title' => 'Contact', 'parent_id' => $parent->id],
            [
                'title'       => 'Contact',
                'parent_id'   => $parent->id,
                'url'         => '/contact',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );
    }

    private function createPolicyMenu(Menu $parent): void
    {
        // Terms of Service
        Menu::firstOrCreate(
            ['title' => 'Terms of Service', 'parent_id' => $parent->id],
            [
                'title'       => 'Terms of Service',
                'parent_id'   => $parent->id,
                'url'         => '/terms-of-service',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Privacy Policy
        Menu::firstOrCreate(
            ['title' => 'Privacy Policy', 'parent_id' => $parent->id],
            [
                'title'       => 'Privacy Policy',
                'parent_id'   => $parent->id,
                'url'         => '/privacy-policy',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Cookie Policy
        Menu::firstOrCreate(
            ['title' => 'Cookie Policy', 'parent_id' => $parent->id],
            [
                'title'       => 'Cookie Policy',
                'parent_id'   => $parent->id,
                'url'         => '/cookie-policy',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );
    }

    private function createSocialMediaMenu(Menu $parent): void
    {
        // TikTok
        Menu::firstOrCreate(
            ['title' => 'TikTok', 'parent_id' => $parent->id],
            [
                'title'       => 'TikTok',
                'parent_id'   => $parent->id,
                'url'         => 'https://www.tiktok.com/@yourcompany',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Blank->value,
                'icon'        => 'bi bi-tiktok',
                'is_external' => true,
                'status'      => 1,
            ]
        );

        // Facebook
        Menu::firstOrCreate(
            ['title' => 'Facebook', 'parent_id' => $parent->id],
            [
                'title'       => 'Facebook',
                'parent_id'   => $parent->id,
                'url'         => 'https://www.facebook.com/yourcompany',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Blank->value,
                'icon'        => 'bi bi-facebook',
                'is_external' => true,
                'status'      => 1,
            ]
        );

        // Twitter
        Menu::firstOrCreate(
            ['title' => 'Twitter', 'parent_id' => $parent->id],
            [
                'title'       => 'Twitter',
                'parent_id'   => $parent->id,
                'url'         => 'https://twitter.com/yourcompany',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Blank->value,
                'icon'        => 'bi bi-twitter-x',
                'is_external' => true,
                'status'      => 1,
            ]
        );

        // YouTube
        Menu::firstOrCreate(
            ['title' => 'YouTube', 'parent_id' => $parent->id],
            [
                'title'       => 'YouTube',
                'parent_id'   => $parent->id,
                'url'         => 'https://www.youtube.com/@yourcompany',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Blank->value,
                'icon'        => 'bi bi-youtube',
                'is_external' => true,
                'status'      => 1,
            ]
        );
    }
}
