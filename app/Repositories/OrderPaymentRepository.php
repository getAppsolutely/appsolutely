<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OrderPayment;

final class OrderPaymentRepository extends BaseRepository
{
    public function model(): string
    {
        return OrderPayment::class;
    }
}
