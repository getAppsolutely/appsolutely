<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Payment;

final class PaymentRepository extends BaseRepository
{
    public function model(): string
    {
        return Payment::class;
    }
}
