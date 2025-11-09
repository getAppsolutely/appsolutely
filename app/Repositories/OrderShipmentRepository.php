<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OrderShipment;

final class OrderShipmentRepository extends BaseRepository
{
    public function model(): string
    {
        return OrderShipment::class;
    }
}
