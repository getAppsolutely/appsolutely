<?php

namespace App\Repositories;

use App\Models\AttributeValue;

class AttributeValueRepository extends BaseRepository
{
    public function model()
    {
        return AttributeValue::class;
    }
}
