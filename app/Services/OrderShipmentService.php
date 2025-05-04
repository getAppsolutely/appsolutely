<?php

namespace App\Services;

use App\Repositories\OrderShipmentRepository;

class OrderShipmentService
{
    public function __construct(protected OrderShipmentRepository $orderShipmentRepository) {}

    public function getOrderShipments($orderId)
    {
        return $this->orderShipmentRepository->findWhere(['order_id' => $orderId]);
    }
}
