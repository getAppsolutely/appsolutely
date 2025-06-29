<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\MenuService;
use Illuminate\Support\Collection;

final class Header extends BaseBlock
{
    public Collection $mainNavigation;

    public Collection $authMenuItems;

    protected array $defaultDisplayOptions = [
        'logo'    => true,
        'booking' => [
            'text' => 'Book A Test Drive',
            'url'  => '/test-drive',
        ],
    ];

    protected array $defaultQueryOptions = [
        'main_navigation' => 'main-navigation',
        'auth_menu'       => 'auth-menu',
        'footer_menu'     => 'footer-menu',
    ];

    protected function initializeComponent(): void
    {
        // Initialize empty collections
        $menuService          = app(MenuService::class);
        $this->mainNavigation = $menuService->getMenusByReference($this->queryOptions['main_navigation']);
        $this->authMenuItems  = $menuService->getMenusByReference($this->queryOptions['auth_menu']);
    }
}
