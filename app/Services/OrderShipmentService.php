<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderShipmentRepository;
use App\Services\Contracts\OrderShipmentServiceInterface;

final class OrderShipmentService implements OrderShipmentServiceInterface
{
    public function __construct(protected OrderShipmentRepository $orderShipmentRepository) {}
}
