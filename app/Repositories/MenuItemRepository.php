<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\MenuItem;
use App\Repositories\Traits\ActiveTreeList;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class MenuItemRepository extends BaseRepository
{
    use ActiveTreeList;

    public function model(): string
    {
        return MenuItem::class;
    }

    public function getActiveMenus(int $menuId, ?Carbon $datetime): Collection
    {
        return $this->model->status()
            ->published($datetime)
            ->byMenu($menuId)
            ->orderBy('left')
            ->get();
    }

    public function getActiveMenuTree(int $menuId, ?Carbon $datetime): \Kalnoy\Nestedset\Collection
    {
        /** @var \Kalnoy\Nestedset\Collection $activeMenus */
        $activeMenus = $this->getActiveMenus($menuId, $datetime);

        return $activeMenus->toTree();
    }
}
