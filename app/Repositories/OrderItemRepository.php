<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository extends BaseRepository
{
    public function model(): string
    {
        return OrderItem::class;
    }
}
