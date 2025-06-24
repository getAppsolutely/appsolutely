<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Repositories\MenuItemRepository;
use App\Repositories\MenuRepository;
use Illuminate\Support\Collection;

final class Footer extends BaseBlock
{
    public Collection $footerMenuItems;

    public Collection $socialMediaItems;

    public Collection $policyMenuItems;

    protected function initializeComponent(): void
    {
        $this->data = array_merge($this->defaultConfig(), $this->data);

        // Initialize empty collections
        $this->footerMenuItems  = collect();
        $this->socialMediaItems = collect();
        $this->policyMenuItems  = collect();

        // Try to load menus if database is available
        try {
            $this->loadMenus();
        } catch (\Exception $e) {
        }
    }

    protected function defaultConfig(): array
    {
        return [
            'logo'         => true,
            'footer_menu'  => 'footer-menu',
            'social_media' => 'social-media',
            'policy_menu'  => 'policy-menu',
            'contact'      => [
                'enabled' => true,
                'phone'   => '+64 9 379 5555',
                'email'   => 'info@company.com',
                'address' => '123 Main Street, Auckland, NZ',
            ],
            'newsletter' => [
                'enabled' => true,
                'title'   => 'Subscribe to our newsletter',
                'text'    => 'Get the latest updates and offers',
            ],
            'copyright' => [
                'text' => '© ' . date('Y') . ' Your Company. All rights reserved.',
            ],
        ];
    }

    private function loadMenus(): void
    {
        $menuRepository     = app(MenuRepository::class);
        $menuItemRepository = app(MenuItemRepository::class);

        // Load footer menu
        $footerMenu            = $menuRepository->findByReference($this->data['footer_menu']);
        $this->footerMenuItems = $footerMenu
            ? $menuItemRepository->getActiveMenuTree($footerMenu->id, now())
            : collect();

        // Load social media menu
        $socialMenu             = $menuRepository->findByReference($this->data['social_media']);
        $this->socialMediaItems = $socialMenu
            ? $menuItemRepository->getActiveMenuTree($socialMenu->id, now())
            : collect();

        // Load policy menu
        $policyMenu            = $menuRepository->findByReference($this->data['policy_menu']);
        $this->policyMenuItems = $policyMenu
            ? $menuItemRepository->getActiveMenuTree($policyMenu->id, now())
            : collect();
    }
}
