<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface PaymentServiceInterface
{
    /**
     * Get all payments
     */
    public function getPayments(): Collection;
}
