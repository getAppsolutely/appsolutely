<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Repositories\MenuItemRepository;
use App\Repositories\MenuRepository;
use Illuminate\Support\Collection;

final class Header extends BaseBlock
{
    public Collection $mainNavigation;

    public Collection $authMenuItems;

    protected function initializeComponent(): void
    {
        $this->data = array_merge($this->defaultConfig(), $this->data);

        // Initialize empty collections
        $this->mainNavigation = collect();
        $this->authMenuItems  = collect();

        // Try to load menus if database is available
        try {
            $this->loadMenus();
        } catch (\Exception $e) {
        }
    }

    protected function defaultConfig(): array
    {
        return [
            'logo'        => true,
            'main_nav'    => 'main-nav',
            'auth_menu'   => 'auth-menu',
            'footer_menu' => 'footer-menu',
            'booking'     => [
                'text' => 'Book A Test Drive',
                'url'  => '/test-drive',
            ],
        ];
    }

    private function loadMenus(): void
    {
        $menuRepository     = app(MenuRepository::class);
        $menuItemRepository = app(MenuItemRepository::class);

        // Load main navigation
        $mainMenu             = $menuRepository->findByReference($this->data['main_nav']);
        $this->mainNavigation = $mainMenu
            ? $menuItemRepository->getActiveMenuTree($mainMenu->id, now())
            : collect();

        // Load auth menu
        $authMenu            = $menuRepository->findByReference($this->data['auth_menu']);
        $this->authMenuItems = $authMenu
            ? $menuItemRepository->getActiveMenuTree($authMenu->id, now())
            : collect();
    }
}
