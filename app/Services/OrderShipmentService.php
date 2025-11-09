<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderShipmentRepository;

final class OrderShipmentService
{
    public function __construct(protected OrderShipmentRepository $orderShipmentRepository) {}
}
