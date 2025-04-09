<?php

namespace App\Repositories\Traits;

use APp\Models\NestedSetModel;

trait ActiveTreeList
{
    // Need NestedSetModel and NodeTrait for the model

    /**
     * @return array|string[]
     */
    public function getActiveList($parentId = null): array
    {
        $tree = $this->getTree($parentId);

        return NestedSetModel::formatTreeArray($tree);
    }

    public function getTree($parentId, $all = false): mixed
    {
        /** @var NestedSetModel $model */
        $model = $this->model;
        if (! empty($parentId)) {
            $model->where('parent_id', $parentId);
        }
        if (! $all) {
            $model = $model->status();
        }

        return $model->get()->toTree();
    }
}
