<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;
use App\Repositories\MenuItemRepository;
use App\Repositories\MenuRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class MenuService
{
    public function __construct(
        protected MenuItemRepository $menuItemRepository,
        protected MenuRepository $menuRepository
    ) {}

    public function getActiveMenuTree($menuId, ?Carbon $datetime = null): Collection
    {
        return $this->menuItemRepository->getActiveMenuTree($menuId, $datetime);
    }

    public function getActiveMenus($menuId, ?Carbon $datetime = null): Collection
    {
        return $this->menuItemRepository->getActiveMenus($menuId, $datetime);
    }

    public function getMenuByReference(string $reference): ?Menu
    {
        return $this->menuRepository->findByReference($reference);
    }
}
