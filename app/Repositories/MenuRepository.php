<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class MenuRepository extends BaseRepository
{
    public function model(): string
    {
        return Menu::class;
    }

    public function getActiveMenus(int $groupId, ?Carbon $datetime): Collection
    {
        return $this->model->status()
            ->published($datetime)
            ->byGroup($groupId)
            ->orderBy('left')
            ->get();
    }

    public function getActiveMenuTree(int $groupId, ?Carbon $datetime): \Kalnoy\Nestedset\Collection
    {
        /** @var \Kalnoy\Nestedset\Collection $activeMenus */
        $activeMenus = $this->getActiveMenus($groupId, $datetime);

        return $activeMenus->toTree();
    }

    public function getActiveList(int $groupId, ?Carbon $datetime): \Kalnoy\Nestedset\Collection
    {
        $tree = $this->getActiveMenuTree($groupId, $datetime);

        return Menu::formatTreeArray($tree);
    }
}
