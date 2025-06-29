<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\MenuService;
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
        'copyright' => [
            'text' => '© %s Your Company. All rights reserved.',
        ],
    ];

    protected array $defaultQueryOptions = [
        'footer_menu'  => 'footer-menu',
        'social_media' => 'social-media',
        'policy_menu'  => 'policy-menu',
    ];

    protected function initializeComponent(): void
    {
        $menuService = app(MenuService::class);

        $this->footerMenuItems  = $menuService->getMenusByReference($this->queryOptions['footer_menu']);
        $this->socialMediaItems = $menuService->getMenusByReference($this->queryOptions['social_media']);
        $this->policyMenuItems  = $menuService->getMenusByReference($this->queryOptions['policy_menu']);
    }
}
