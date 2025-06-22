<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Repositories\MenuItemRepository;
use App\Repositories\MenuRepository;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Footer extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $config;

    public Collection $footerMenuItems;

    public Collection $socialMediaItems;

    public Collection $policyMenuItems;

    /**
     * Mount the component with configuration array.
     *
     * @param  array<string, mixed>  $config
     */
    public function mount(array $config = []): void
    {
        $this->config = array_merge($this->defaultConfig(), $config);

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

    /**
     * Get default configuration.
     *
     * @return array<string, mixed>
     */
    private function defaultConfig(): array
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
        $footerMenu            = $menuRepository->findByReference($this->config['footer_menu']);
        $this->footerMenuItems = $footerMenu
            ? $menuItemRepository->getActiveMenuTree($footerMenu->id, now())
            : collect();

        // Load social media menu
        $socialMenu             = $menuRepository->findByReference($this->config['social_media']);
        $this->socialMediaItems = $socialMenu
            ? $menuItemRepository->getActiveMenuTree($socialMenu->id, now())
            : collect();

        // Load policy menu
        $policyMenu            = $menuRepository->findByReference($this->config['policy_menu']);
        $this->policyMenuItems = $policyMenu
            ? $menuItemRepository->getActiveMenuTree($policyMenu->id, now())
            : collect();
    }

    public function render(): object
    {
        return themed_view('livewire.footer');
    }
}
