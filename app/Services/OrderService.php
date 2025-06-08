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
}
