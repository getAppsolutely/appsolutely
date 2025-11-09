<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OrderItem;

final class OrderItemRepository extends BaseRepository
{
    public function model(): string
    {
        return OrderItem::class;
    }
}
