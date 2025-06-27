<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Menu;
use App\Repositories\Traits\ActiveTreeList;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class MenuRepository extends BaseRepository
{
    use ActiveTreeList;

    public function model(): string
    {
        return Menu::class;
    }

    public function getActiveMenus(int $menuId, ?Carbon $datetime): Collection
    {
        return $this->model->status()
            ->published($datetime)
            ->where('parent_id', $menuId)
            ->orderBy('left')
            ->get();
    }

    public function getActiveMenuTree(int $menuId, ?Carbon $datetime): \Kalnoy\Nestedset\Collection
    {
        /** @var \Kalnoy\Nestedset\Collection $activeMenus */
        $activeMenus = $this->getActiveMenus($menuId, $datetime);

        return $activeMenus->toTree();
    }

    public function findByReference(string $reference): ?Menu
    {
        return $this->model->with(['children'])->where('reference', $reference)->firstOrFail();
    }
}
