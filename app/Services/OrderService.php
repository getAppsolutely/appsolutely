<?php

namespace App\Services;

use App\Repositories\OrderItemRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderShipmentRepository;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository,
        protected OrderPaymentRepository $orderPaymentRepository,
        protected OrderShipmentRepository $orderShipmentRepository,
    ) {}

    public function getOrders()
    {
        return $this->orderRepository->all();
    }

    public function getOrderItems($orderId)
    {
        return $this->orderItemRepository->findWhere(['order_id' => $orderId]);
    }

    public function getOrderPayments($orderId)
    {
        return $this->orderPaymentRepository->findWhere(['order_id' => $orderId]);
    }

    public function getOrderShipments($orderId)
    {
        return $this->orderShipmentRepository->findWhere(['order_id' => $orderId]);
    }
}
