<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\MenuService;
use Illuminate\Support\Collection;

final class Header extends BaseBlock
{
    public Collection $mainNavigation;

    public Collection $authMenuItems;

    public array $queryOptions = [
        'main_navigation' => 'main-navigation',
        'auth_menu'       => 'auth-menu',
        'footer_menu'     => 'footer-menu',
    ];

    public array $displayOptions = [
        'logo'    => true,
        'booking' => [
            'text' => 'Book A Test Drive',
            'url'  => '/test-drive',
        ],
    ];

    protected function initializeComponent(): void
    {
        // Initialize empty collections
        $this->mainNavigation = collect();
        $this->authMenuItems  = collect();

        // Try to load menus if database is available
        try {
            $this->loadMenus();
        } catch (\Exception $e) {
            log_error($e->getMessage(), [], __CLASS__, __METHOD__);
        }
    }

    protected function loadMenus(): void
    {
        $menuService     = app(MenuService::class);

        $this->mainNavigation = $menuService->getMenusByReference($this->queryOptions['main_navigation']);
        $this->authMenuItems  = $menuService->getMenusByReference($this->queryOptions['auth_menu']);
    }
}
