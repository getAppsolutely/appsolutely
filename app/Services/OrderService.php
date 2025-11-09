<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderItemRepository;
use App\Repositories\OrderPaymentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderShipmentRepository;
use App\Services\Contracts\OrderServiceInterface;

final readonly class OrderService implements OrderServiceInterface
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository,
        protected OrderPaymentRepository $orderPaymentRepository,
        protected OrderShipmentRepository $orderShipmentRepository,
    ) {}
}
