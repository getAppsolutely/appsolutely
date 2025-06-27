<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\MenuService;
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
            'logo'            => true,
            'main_navigation' => 'main-navigation',
            'auth_menu'       => 'auth-menu',
            'footer_menu'     => 'footer-menu',
            'booking'         => [
                'text' => 'Book A Test Drive',
                'url'  => '/test-drive',
            ],
        ];
    }

    private function loadMenus(): void
    {
        $menuService     = app(MenuService::class);

        $this->mainNavigation = $menuService->getMenusByReference($this->data['main_navigation']);
        $this->authMenuItems  = $menuService->getMenusByReference($this->data['auth_menu']);
    }
}
