<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserAddressRepository;

final class UserAddressService
{
    public function __construct(
        protected UserAddressRepository $userAddressRepository,
    ) {}
}
