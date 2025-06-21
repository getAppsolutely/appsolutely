<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Repositories\MenuItemRepository;
use App\Repositories\MenuRepository;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Header extends Component
{
    public string $logo;

    public string $mainNav;

    public string $authMenu;

    public Collection $mainNavigation;

    public Collection $authMenuItems;

    public function mount(
        ?string $logo = null,
        ?string $mainNav = null,
        ?string $authMenu = null
    ): void {
        $this->logo     = $logo ?? config('appsolutely.general.logo');
        $this->mainNav  = $mainNav ?? 'main-nav';
        $this->authMenu = $authMenu ?? 'auth-menu';

        $this->loadMenus();
    }

    private function loadMenus(): void
    {
        $menuRepository     = app(MenuRepository::class);
        $menuItemRepository = app(MenuItemRepository::class);

        // Load main navigation
        $mainMenu             = $menuRepository->findByReference($this->mainNav);
        $this->mainNavigation = $mainMenu
            ? $menuItemRepository->getActiveMenuTree($mainMenu->id, now())
            : collect();

        // Load auth menu
        $authMenu            = $menuRepository->findByReference($this->authMenu);
        $this->authMenuItems = $authMenu
            ? $menuItemRepository->getActiveMenuTree($authMenu->id, now())
            : collect();
    }

    public function render(): object
    {
        return themed_view('livewire.header');
    }
}
