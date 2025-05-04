<?php

namespace App\Services;

use App\Repositories\OrderPaymentRepository;
use App\Repositories\PaymentRepository;

class PaymentService
{
    public function __construct(
        protected PaymentRepository $paymentRepository,
        protected OrderPaymentRepository $orderPaymentRepository,
    ) {}

    public function getPayments()
    {
        return $this->paymentRepository->all();
    }
}
