<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserAddressRepository;
use App\Services\Contracts\UserAddressServiceInterface;

final readonly class UserAddressService implements UserAddressServiceInterface
{
    public function __construct(
        protected UserAddressRepository $userAddressRepository,
    ) {}
}
