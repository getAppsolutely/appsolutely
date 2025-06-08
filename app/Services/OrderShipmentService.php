<?php

namespace App\Services;

use App\Repositories\OrderShipmentRepository;

class OrderShipmentService
{
    public function __construct(protected OrderShipmentRepository $orderShipmentRepository) {}
}
