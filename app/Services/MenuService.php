<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;
use App\Repositories\MenuRepository;
use App\Services\Contracts\MenuServiceInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class MenuService implements MenuServiceInterface
{
    public function __construct(
        protected MenuRepository $menuRepository
    ) {}

    public function getActiveMenuTree($menuId, ?Carbon $datetime = null): Collection
    {
        return $this->menuRepository->getActiveMenuTree($menuId, $datetime);
    }

    public function getActiveMenus($menuId, ?Carbon $datetime = null): Collection
    {
        return $this->menuRepository->getActiveMenus($menuId, $datetime);
    }

    public function findByReference(string $reference): ?Menu
    {
        return $this->menuRepository->findByReference($reference);
    }

    public function getMenusByReference(string $reference): \Illuminate\Support\Collection
    {
        $menu = $this->menuRepository->findByReference($reference);

        return $menu->children ?? collect();
    }
}
