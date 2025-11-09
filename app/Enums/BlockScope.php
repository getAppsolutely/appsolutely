<?php

declare(strict_types=1);

namespace App\Enums;

enum BlockScope: string
{
    case Page     = 'page';
    case Global   = 'global';

    public function toArray(): string
    {
        return match ($this) {
            self::Page   => 'Page',
            self::Global => 'Global',
        };
    }
}
