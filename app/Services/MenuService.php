<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MenuGroup;
use App\Repositories\MenuGroupRepository;
use App\Repositories\MenuRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class MenuService
{
    public function __construct(
        protected MenuRepository $menuRepository,
        protected MenuGroupRepository $menuGroupRepository
    ) {}

    public function getActiveMenuTree($groupId, ?Carbon $datetime = null): Collection
    {
        return $this->menuRepository->getActiveMenuTree($groupId, $datetime);
    }

    public function getActiveMenus($groupId, ?Carbon $datetime = null): Collection
    {
        return $this->menuRepository->getActiveMenus($groupId, $datetime);
    }

    public function getMenuGroupByReference(string $reference): ?MenuGroup
    {
        return $this->menuGroupRepository->findByReference($reference);
    }

    public function getActiveMenuTreeByReference(string $reference, ?Carbon $datetime = null): Collection
    {
        $group = $this->getMenuGroupByReference($reference);
        if (! $group) {
            return collect();
        }

        return $this->getActiveMenuTree($group->id, $datetime);
    }

    public function getActiveMenusByReference(string $reference, ?Carbon $datetime = null): Collection
    {
        $group = $this->getMenuGroupByReference($reference);
        if (! $group) {
            return collect();
        }

        return $this->getActiveMenus($group->id, $datetime);
    }
}
