<?php

declare(strict_types=1);

namespace App\Enums;

enum Status: int
{
    case INACTIVE = 0;
    case ACTIVE   = 1;

    public static function toArray(): array
    {
        return [
            self::INACTIVE->value => 'Inactive',
            self::ACTIVE->value   => 'Active',
        ];
    }
}
