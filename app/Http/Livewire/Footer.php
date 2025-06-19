<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Repositories\MenuItemRepository;
use App\Repositories\MenuRepository;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Footer extends Component
{
    public string $footerMenu;

    public string $socialMedia;

    public string $policyMenu;

    public Collection $footerMenuItems;

    public Collection $socialMediaItems;

    public Collection $policyMenuItems;

    public function mount(
        ?string $footerMenu = null,
        ?string $socialMedia = null,
        ?string $policyMenu = null
    ): void {
        $this->footerMenu  = $footerMenu ?? 'main-nav';
        $this->socialMedia = $socialMedia ?? 'social-media';
        $this->policyMenu  = $policyMenu ?? 'footer-menu';

        $this->loadMenus();
    }

    private function loadMenus(): void
    {
        $menuRepository     = app(MenuRepository::class);
        $menuItemRepository = app(MenuItemRepository::class);

        // Load footer menu
        $footerMenu            = $menuRepository->findByReference($this->footerMenu);
        $this->footerMenuItems = $footerMenu
            ? $menuItemRepository->getActiveMenuTree($footerMenu->id, now())
            : collect();

        // Load social media menu
        $socialMenu             = $menuRepository->findByReference($this->socialMedia);
        $this->socialMediaItems = $socialMenu
            ? $menuItemRepository->getActiveMenuTree($socialMenu->id, now())
            : collect();

        // Load policy menu
        $policyMenu            = $menuRepository->findByReference($this->policyMenu);
        $this->policyMenuItems = $policyMenu
            ? $menuItemRepository->getActiveMenuTree($policyMenu->id, now())
            : collect();
    }

    public function render(): object
    {
        return themed_view('livewire.footer');
    }
}
