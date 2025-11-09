<?php

namespace App\Services;

use App\Repositories\UserAddressRepository;

final class UserAddressService
{
    public function __construct(
        protected UserAddressRepository $userAddressRepository,
    ) {}
}
