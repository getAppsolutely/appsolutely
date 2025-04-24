<?php

namespace App\Repositories;

use App\Models\AttributeGroup;

class AttributeGroupRepository extends BaseRepository
{
    public function model()
    {
        return AttributeGroup::class;
    }
}
