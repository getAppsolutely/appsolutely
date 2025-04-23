<?php

namespace App\Repositories;

use App\Models\AttributeGroup;

class AttributeGroupRepository extends BaseRepository
{
    public function __construct(AttributeGroup $model)
    {
        parent::__construct($model);
    }
}
