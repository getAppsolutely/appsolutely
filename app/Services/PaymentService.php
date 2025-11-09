<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\OrderPaymentRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Database\Eloquent\Collection;

final class PaymentService
{
    public function __construct(
        protected PaymentRepository $paymentRepository,
        protected OrderPaymentRepository $orderPaymentRepository,
    ) {}

    public function getPayments(): Collection
    {
        return $this->paymentRepository->all();
    }
}
