<?php

namespace Database\Seeders;

use App\Enums\MenuTarget;
use App\Enums\MenuType;
use App\Models\Menu;
use App\Models\MenuGroup;
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
        // Create menu groups first
        $this->createMenuGroups();

        // Then create menu items
        $mainGroup   = MenuGroup::where('title', 'Main Navigation')->first();
        $footerGroup = MenuGroup::where('title', 'Footer Menu')->first();

        if ($mainGroup) {
            $this->createMainNavigation($mainGroup);
        }

        if ($footerGroup) {
            $this->createFooterMenu($footerGroup);
        }
    }

    private function createMenuGroups(): void
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
            MenuGroup::firstOrCreate(
                ['reference' => $group['reference']],
                $group
            );
        }
    }

    private function createMainNavigation(MenuGroup $group): void
    {
        // Home
        $home = Menu::firstOrCreate(
            ['title' => 'Home', 'menu_group_id' => $group->id],
            [
                'title'         => 'Home',
                'menu_group_id' => $group->id,
                'route'         => '/',
                'type'          => MenuType::Link->value,
                'target'        => MenuTarget::Self->value,
                'is_external'   => false,
                'status'        => 1,
            ]
        );

        // About
        $about = Menu::firstOrCreate(
            ['title' => 'About', 'menu_group_id' => $group->id],
            [
                'title'         => 'About',
                'menu_group_id' => $group->id,
                'route'         => '/about',
                'type'          => MenuType::Link->value,
                'target'        => MenuTarget::Self->value,
                'is_external'   => false,
                'status'        => 1,
            ]
        );

        // Services dropdown
        $services = Menu::firstOrCreate(
            ['title' => 'Services', 'menu_group_id' => $group->id],
            [
                'title'         => 'Services',
                'menu_group_id' => $group->id,
                'route'         => '/services',
                'type'          => MenuType::Dropdown->value,
                'target'        => MenuTarget::Self->value,
                'is_external'   => false,
                'status'        => 1,
            ]
        );

        // Service sub-items
        Menu::firstOrCreate(
            ['title' => 'Web Development', 'menu_group_id' => $group->id, 'parent_id' => $services->id],
            [
                'title'         => 'Web Development',
                'menu_group_id' => $group->id,
                'parent_id'     => $services->id,
                'route'         => '/services/web-development',
                'type'          => MenuType::Link->value,
                'target'        => MenuTarget::Self->value,
                'is_external'   => false,
                'status'        => 1,
            ]
        );

        Menu::firstOrCreate(
            ['title' => 'Mobile Apps', 'menu_group_id' => $group->id, 'parent_id' => $services->id],
            [
                'title'         => 'Mobile Apps',
                'menu_group_id' => $group->id,
                'parent_id'     => $services->id,
                'route'         => '/services/mobile-apps',
                'type'          => MenuType::Link->value,
                'target'        => MenuTarget::Self->value,
                'is_external'   => false,
                'status'        => 1,
            ]
        );

        // Contact
        Menu::firstOrCreate(
            ['title' => 'Contact', 'menu_group_id' => $group->id],
            [
                'title'         => 'Contact',
                'menu_group_id' => $group->id,
                'route'         => '/contact',
                'type'          => MenuType::Link->value,
                'target'        => MenuTarget::Self->value,
                'is_external'   => false,
                'status'        => 1,
            ]
        );
    }

    private function createFooterMenu(MenuGroup $group): void
    {
        // Privacy Policy
        Menu::firstOrCreate(
            ['title' => 'Privacy Policy', 'menu_group_id' => $group->id],
            [
                'title'         => 'Privacy Policy',
                'menu_group_id' => $group->id,
                'route'         => '/privacy-policy',
                'type'          => MenuType::Link->value,
                'target'        => MenuTarget::Self->value,
                'is_external'   => false,
                'status'        => 1,
            ]
        );

        // Terms of Service
        Menu::firstOrCreate(
            ['title' => 'Terms of Service', 'menu_group_id' => $group->id],
            [
                'title'         => 'Terms of Service',
                'menu_group_id' => $group->id,
                'route'         => '/terms-of-service',
                'type'          => MenuType::Link->value,
                'target'        => MenuTarget::Self->value,
                'is_external'   => false,
                'status'        => 1,
            ]
        );

        // Divider
        Menu::firstOrCreate(
            ['title' => 'Footer Divider', 'menu_group_id' => $group->id],
            [
                'title'         => 'Footer Divider',
                'menu_group_id' => $group->id,
                'type'          => MenuType::Divider->value,
                'status'        => 1,
            ]
        );

        // External Link Example
        Menu::firstOrCreate(
            ['title' => 'Documentation', 'menu_group_id' => $group->id],
            [
                'title'         => 'Documentation',
                'menu_group_id' => $group->id,
                'route'         => 'https://docs.example.com',
                'type'          => MenuType::Link->value,
                'target'        => MenuTarget::Blank->value,
                'is_external'   => true,
                'status'        => 1,
            ]
        );
    }
}
