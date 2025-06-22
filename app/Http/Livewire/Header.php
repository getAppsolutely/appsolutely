<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Repositories\MenuItemRepository;
use App\Repositories\MenuRepository;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Header extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $config;

    public Collection $mainNavigation;

    public Collection $authMenuItems;

    /**
     * Mount the component with configuration array.
     *
     * @param  array<string, mixed>  $config
     */
    public function mount(array $config = []): void
    {
        $this->config = array_merge($this->defaultConfig(), $config);

        // Initialize empty collections
        $this->mainNavigation = collect();
        $this->authMenuItems  = collect();

        // Try to load menus if database is available
        try {
            $this->loadMenus();
        } catch (\Exception $e) {
        }
    }

    /**
     * Get default configuration.
     *
     * @return array<string, mixed>
     */
    private function defaultConfig(): array
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
        $mainMenu             = $menuRepository->findByReference($this->config['main_nav']);
        $this->mainNavigation = $mainMenu
            ? $menuItemRepository->getActiveMenuTree($mainMenu->id, now())
            : collect();

        // Load auth menu
        $authMenu            = $menuRepository->findByReference($this->config['auth_menu']);
        $this->authMenuItems = $authMenu
            ? $menuItemRepository->getActiveMenuTree($authMenu->id, now())
            : collect();
    }

    public function render(): object
    {
        return themed_view('livewire.header');
    }
}
