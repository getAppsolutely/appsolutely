<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserAddress;

final class UserAddressRepository extends BaseRepository
{
    public function model(): string
    {
        return UserAddress::class;
    }
}
