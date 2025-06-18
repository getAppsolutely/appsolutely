<?php

namespace Database\Seeders;

use App\Enums\MenuTarget;
use App\Enums\MenuType;
use App\Models\Menu;
use App\Models\MenuItem;
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
        $this->createMenus();

        // Then create menu items
        $mainGroup   = Menu::where('title', 'Main Navigation')->first();
        $footerGroup = Menu::where('title', 'Footer Menu')->first();

        if ($mainGroup) {
            $this->createMainNavigation($mainGroup);
        }

        if ($footerGroup) {
            $this->createFooterMenu($footerGroup);
        }
    }

    private function createMenus(): void
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
        ];

        foreach ($groups as $group) {
            Menu::firstOrCreate(
                ['reference' => $group['reference']],
                $group
            );
        }
    }

    private function createMainNavigation(Menu $group): void
    {
        // Home
        $home = MenuItem::firstOrCreate(
            ['title' => 'Home', 'menu_id' => $group->id],
            [
                'title'       => 'Home',
                'menu_id'     => $group->id,
                'route'       => '/',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // About
        $about = MenuItem::firstOrCreate(
            ['title' => 'About', 'menu_id' => $group->id],
            [
                'title'       => 'About',
                'menu_id'     => $group->id,
                'route'       => '/about',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Services dropdown
        $services = MenuItem::firstOrCreate(
            ['title' => 'Services', 'menu_id' => $group->id],
            [
                'title'       => 'Services',
                'menu_id'     => $group->id,
                'route'       => '/services',
                'type'        => MenuType::Dropdown->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Service sub-items
        MenuItem::firstOrCreate(
            ['title' => 'Web Development', 'menu_id' => $group->id, 'parent_id' => $services->id],
            [
                'title'       => 'Web Development',
                'menu_id'     => $group->id,
                'parent_id'   => $services->id,
                'route'       => '/services/web-development',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        MenuItem::firstOrCreate(
            ['title' => 'Mobile Apps', 'menu_id' => $group->id, 'parent_id' => $services->id],
            [
                'title'       => 'Mobile Apps',
                'menu_id'     => $group->id,
                'parent_id'   => $services->id,
                'route'       => '/services/mobile-apps',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Contact
        MenuItem::firstOrCreate(
            ['title' => 'Contact', 'menu_id' => $group->id],
            [
                'title'       => 'Contact',
                'menu_id'     => $group->id,
                'route'       => '/contact',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );
    }

    private function createFooterMenu(Menu $group): void
    {
        // Privacy Policy
        MenuItem::firstOrCreate(
            ['title' => 'Privacy Policy', 'menu_id' => $group->id],
            [
                'title'       => 'Privacy Policy',
                'menu_id'     => $group->id,
                'route'       => '/privacy-policy',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Terms of Service
        MenuItem::firstOrCreate(
            ['title' => 'Terms of Service', 'menu_id' => $group->id],
            [
                'title'       => 'Terms of Service',
                'menu_id'     => $group->id,
                'route'       => '/terms-of-service',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Self->value,
                'is_external' => false,
                'status'      => 1,
            ]
        );

        // Divider
        MenuItem::firstOrCreate(
            ['title' => 'Footer Divider', 'menu_id' => $group->id],
            [
                'title'   => 'Footer Divider',
                'menu_id' => $group->id,
                'type'    => MenuType::Divider->value,
                'status'  => 1,
            ]
        );

        // External Link Example
        MenuItem::firstOrCreate(
            ['title' => 'Documentation', 'menu_id' => $group->id],
            [
                'title'       => 'Documentation',
                'menu_id'     => $group->id,
                'route'       => 'https://docs.example.com',
                'type'        => MenuType::Link->value,
                'target'      => MenuTarget::Blank->value,
                'is_external' => true,
                'status'      => 1,
            ]
        );
    }
}
