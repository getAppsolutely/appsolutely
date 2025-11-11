<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\Contracts\MenuServiceInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

final class Footer extends BaseBlock
{
    public Collection $footerMenuItems;

    public Collection $socialMediaItems;

    public Collection $policyMenuItems;

    protected array $defaultDisplayOptions = [
        'logo'    => true,
        'contact' => [
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
        'company_name' => '',
        'address'      => '',
        'email'        => '',
        'copyright'    => [
            'text' => '© 2025 Your Company. All rights reserved.',
        ],
    ];

    protected array $defaultQueryOptions = [
        'footer_menu'  => 'footer-menu',
        'social_media' => 'social-media',
        'policy_menu'  => 'policy-menu',
    ];

    protected function initializeComponent(Container $container): void
    {
        // Resolve MenuService from container (Livewire doesn't support constructor injection)
        $menuService = $container->make(MenuServiceInterface::class);

        $this->footerMenuItems  = $menuService->getMenusByReference($this->queryOptions['footer_menu']);
        $this->socialMediaItems = $menuService->getMenusByReference($this->queryOptions['social_media']);
        $this->policyMenuItems  = $menuService->getMenusByReference($this->queryOptions['policy_menu']);
    }
}
