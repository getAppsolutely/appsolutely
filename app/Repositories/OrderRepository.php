<?php

namespace App\Repositories;

use App\Models\Order;

final class OrderRepository extends BaseRepository
{
    public function model(): string
    {
        return Order::class;
    }
}
