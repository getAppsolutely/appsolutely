<?php

namespace App\Services;

use App\Repositories\UserAddressRepository;

class UserAddressService
{
    public function __construct(
        protected UserAddressRepository $userAddressRepository,
    ) {}
}
