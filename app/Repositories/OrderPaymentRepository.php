<?php

namespace App\Repositories;

use App\Models\OrderPayment;

class OrderPaymentRepository extends BaseRepository
{
    public function model(): string
    {
        return OrderPayment::class;
    }
}
