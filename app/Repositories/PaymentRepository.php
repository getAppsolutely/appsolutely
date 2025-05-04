<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository extends BaseRepository
{
    public function model(): string
    {
        return Payment::class;
    }
}
