<?php

namespace App\Repositories;

use App\Models\OrderShipment;

class OrderShipmentRepository extends BaseRepository
{
    public function model(): string
    {
        return OrderShipment::class;
    }
}
