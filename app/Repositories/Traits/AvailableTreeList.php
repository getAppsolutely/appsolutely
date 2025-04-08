<?php

namespace App\Repositories\Traits;

trait AvailableTreeList
{
    // Need NestedSetModel and NodeTrait for the model

    /**
     * @return array|string[]
     */
    public function getAvailableTreeList($parentId = null): array
    {
        $tree     = $this->getAvailableTree($parentId);
        $data     = [];
        $traverse = function ($categories, $prefix = '') use (&$traverse, &$data) {
            foreach ($categories as $category) {
                $item = [(string) $category->id => $prefix . ' ' . $category->title];
                $data = $data + $item;
                $traverse($category->children, $prefix . '--');
            }
        };

        $traverse($tree);

        return $data;
    }

    public function getAvailableTree($parentId): mixed
    {
        $model = $this->model;
        if (! empty($parentId)) {
            $model->where('parent_id', $parentId);
        }

        return $model->get()->toTree();
    }
}
