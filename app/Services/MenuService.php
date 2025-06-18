<?php

declare(strict_types=1);

namespace App\Services;

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
}
